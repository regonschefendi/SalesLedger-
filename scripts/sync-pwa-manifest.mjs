import { copyFileSync, existsSync } from 'node:fs';
import { dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';

const root = resolve(dirname(fileURLToPath(import.meta.url)), '..');
const source = resolve(root, 'public/build/manifest.webmanifest');
const target = resolve(root, 'public/manifest.webmanifest');

if (existsSync(source)) {
    copyFileSync(source, target);
}
