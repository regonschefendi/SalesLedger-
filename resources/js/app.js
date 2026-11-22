const supportsSpeculationRules =
    typeof HTMLScriptElement !== 'undefined' &&
    typeof HTMLScriptElement.supports === 'function' &&
    HTMLScriptElement.supports('speculationrules');

const prefetchedUrls = new Set();
let progressTimer = null;
let progressSafetyTimer = null;
let progressBar = null;
let navPrerenderAdded = false;

function connectionAllowsPrefetch() {
    const connection =
        navigator.connection ||
        navigator.mozConnection ||
        navigator.webkitConnection;

    if (!connection) {
        return true;
    }

    if (connection.saveData) {
        return false;
    }

    return !/(^|-)2g$/.test(connection.effectiveType || '');
}

function resolveInternalUrl(link) {
    if (!link || !link.getAttribute) {
        return null;
    }

    if (
        link.hasAttribute('download') ||
        link.hasAttribute('data-no-prefetch') ||
        link.target === '_blank' ||
        link.target === '_parent' ||
        link.target === '_top'
    ) {
        return null;
    }

    const href = link.getAttribute('href');

    if (!href || href.charAt(0) === '#' || /^(mailto:|tel:|javascript:|blob:|data:)/i.test(href)) {
        return null;
    }

    let url;

    try {
        url = new URL(href, window.location.href);
    } catch (error) {
        return null;
    }

    if (url.origin !== window.location.origin) {
        return null;
    }

    if (url.protocol !== 'http:' && url.protocol !== 'https:') {
        return null;
    }

    if (/\/download$/.test(url.pathname) || /\/export$/.test(url.pathname)) {
        return null;
    }

    return url;
}

function isSamePage(url) {
    return url.pathname === window.location.pathname && url.search === window.location.search;
}

function addSpeculationRules(rules) {
    const script = document.createElement('script');
    script.type = 'speculationrules';
    script.textContent = JSON.stringify(rules);
    document.head.appendChild(script);
}

function prerenderNavigationTargets() {
    if (navPrerenderAdded || !supportsSpeculationRules || !connectionAllowsPrefetch()) {
        return;
    }

    if (document.visibilityState !== 'visible') {
        return;
    }

    const containers = document.querySelectorAll('[data-nav-prerender]');
    const urls = [];

    containers.forEach((container) => {
        container.querySelectorAll('a[href]').forEach((link) => {
            const url = resolveInternalUrl(link);

            if (!url || isSamePage(url)) {
                return;
            }

            const target = url.pathname + url.search;

            if (urls.indexOf(target) === -1) {
                urls.push(target);
            }
        });
    });

    if (urls.length === 0) {
        return;
    }

    navPrerenderAdded = true;

    addSpeculationRules({
        prerender: [
            {
                urls: urls,
                eagerness: 'immediate',
            },
        ],
    });
}

function prefetchLink(link) {
    const url = resolveInternalUrl(link);

    if (!url || isSamePage(url) || prefetchedUrls.has(url.href)) {
        return;
    }

    prefetchedUrls.add(url.href);

    fetch(url.href, {
        method: 'GET',
        credentials: 'same-origin',
        headers: { Accept: 'text/html,application/xhtml+xml' },
    })
        .then((response) => response.text())
        .catch(() => {});
}

function bindIntentPrefetch() {
    if (supportsSpeculationRules) {
        return;
    }

    const prefetchFromEvent = (event) => {
        const link = event.target && event.target.closest ? event.target.closest('a[href]') : null;

        if (link) {
            prefetchLink(link);
        }
    };

    ['pointerover', 'pointerdown', 'focusin', 'touchstart'].forEach((eventName) => {
        document.addEventListener(eventName, prefetchFromEvent, { capture: true, passive: true });
    });
}

function ensureProgressBar() {
    if (progressBar && progressBar.isConnected) {
        return progressBar;
    }

    progressBar = document.createElement('div');
    progressBar.id = 'nav-progress';
    progressBar.setAttribute('aria-hidden', 'true');
    progressBar.style.cssText = [
        'position:fixed',
        'top:0',
        'left:0',
        'right:0',
        'height:3px',
        'z-index:2147483647',
        'pointer-events:none',
        'transform:scaleX(0)',
        'transform-origin:left center',
        'background:#0F47A1',
        'opacity:0',
        'transition:transform 450ms cubic-bezier(.1,.7,.2,1), opacity 200ms ease',
    ].join(';');

    document.body.appendChild(progressBar);

    return progressBar;
}

function beginNavigation(event) {
    if (progressTimer || progressBar) {
        return;
    }

    progressTimer = window.setTimeout(() => {
        progressTimer = null;

        if (event && event.defaultPrevented) {
            return;
        }

        const bar = ensureProgressBar();
        bar.style.opacity = '1';

        progressSafetyTimer = window.setTimeout(completeNavigation, 10000);

        window.requestAnimationFrame(() => {
            bar.style.transform = 'scaleX(0.75)';
        });
    }, 120);
}

function completeNavigation() {
    if (progressTimer) {
        window.clearTimeout(progressTimer);
        progressTimer = null;
    }

    if (progressSafetyTimer) {
        window.clearTimeout(progressSafetyTimer);
        progressSafetyTimer = null;
    }

    if (!progressBar) {
        return;
    }

    const bar = progressBar;
    progressBar = null;
    bar.style.transform = 'scaleX(1)';
    bar.style.opacity = '0';

    window.setTimeout(() => {
        if (bar.parentNode) {
            bar.parentNode.removeChild(bar);
        }
    }, 250);
}

function bindProgressIndicator() {
    document.addEventListener('click', (event) => {
        if (
            event.defaultPrevented ||
            event.button !== 0 ||
            event.metaKey ||
            event.ctrlKey ||
            event.shiftKey ||
            event.altKey
        ) {
            return;
        }

        const link = event.target && event.target.closest ? event.target.closest('a[href]') : null;

        if (!link) {
            return;
        }

        const url = resolveInternalUrl(link);

        if (!url || isSamePage(url)) {
            return;
        }

        beginNavigation(event);
    });

    window.addEventListener('pageshow', completeNavigation);
    window.addEventListener('pagehide', completeNavigation);
}

function schedulePrerender() {
    const run = () => prerenderNavigationTargets();

    if ('requestIdleCallback' in window) {
        window.requestIdleCallback(run, { timeout: 2500 });
    } else {
        window.setTimeout(run, 1200);
    }

    document.addEventListener('visibilitychange', () => {
        if (document.visibilityState === 'visible') {
            run();
        }
    });
}

function boot() {
    bindProgressIndicator();
    bindIntentPrefetch();
    schedulePrerender();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot, { once: true });
} else {
    boot();
}
