import * as esbuild from 'esbuild';
import { readFileSync, writeFileSync } from 'fs';
import { createHash } from 'crypto';

const isWatch = process.argv.includes('--watch');

/** @type {esbuild.BuildOptions} */
const jsConfig = {
  entryPoints: ['src/js/main.js'],
  outfile: 'dashboard/assets/js/pocket_rewards_app.js',
  bundle: true,
  minify: true,
  sourcemap: false,
  target: ['es2020'],
  format: 'iife',
};

/** @type {esbuild.BuildOptions} */
const cssConfig = {
  entryPoints: ['src/css/notifications.css', 'src/css/utilities.css'],
  outdir: 'dashboard/assets/css',
  bundle: true,
  minify: true,
  loader: { '.css': 'css' },
};

function hashFile(filePath) {
  const content = readFileSync(filePath);
  return createHash('md5').update(content).digest('hex').slice(0, 8);
}

async function build() {
  if (isWatch) {
    const jsCtx = await esbuild.context(jsConfig);
    await jsCtx.watch();
    const cssCtx = await esbuild.context(cssConfig);
    await cssCtx.watch();
    console.log('[flycash] Watching for changes...');
  } else {
    await Promise.all([
      esbuild.build(jsConfig),
      esbuild.build(cssConfig),
    ]);

    // Generate version manifest for cache busting
    const jsHash = hashFile('dashboard/assets/js/pocket_rewards_app.js');
    const cssHash = hashFile('dashboard/assets/css/notifications.bundle.css');
    const utilsHash = hashFile('dashboard/assets/css/utilities.bundle.css');

    const manifest = {
      'pocket_rewards_app.js': { hash: jsHash },
      'notifications.bundle.css': { hash: cssHash },
      'utilities.bundle.css': { hash: utilsHash },
      version: Date.now(),
    };

    writeFileSync('dashboard/assets/manifest.json', JSON.stringify(manifest, null, 2));
    console.log('[flycash] Build complete');
    console.log(`  JS:  pocket_rewards_app.js (${jsHash})`);
    console.log(`  CSS: notifications.bundle.css (${cssHash})`);
    console.log(`  CSS: utilities.bundle.css (${utilsHash})`);
  }
}

build();
