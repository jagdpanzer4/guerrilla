import { defineConfig } from 'vite';
import { resolve } from 'path';

export default defineConfig({
    build: {
        lib: {
            entry: resolve(__dirname, 'src/material-web.js'),
            name: 'GuerrillaWeb',
            fileName: () => 'material-web',
            formats: ['iife'],
        },
        outDir: resolve(__dirname, 'themes/guerrilla/js/dist'),
        emptyOutDir: false,
        minify: true,
        rollupOptions: {
            output: {
                entryFileNames: 'material-web.js',
            },
        },
    },
});
