import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/modules/trust-badges.js',
                'resources/css/admin.css',
                'resources/js/admin.js'
            ],
            refresh: true,
        }),
    ],
    build: {
        // Output directory for compiled assets
        outDir: 'public/build',
        
        // Generate manifest for asset versioning
        manifest: 'manifest.json',
        
        // Rollup options for optimization
        rollupOptions: {
            output: {
                // Manual chunks for better caching
                manualChunks: undefined,
                
                // Asset file naming
                assetFileNames: (assetInfo) => {
                    const info = assetInfo.name.split('.');
                    const ext = info[info.length - 1];
                    
                    if (/\.(png|jpe?g|svg|gif|tiff|bmp|ico|webp)$/i.test(assetInfo.name)) {
                        return `images/[name]-[hash].${ext}`;
                    }
                    
                    if (/\.(woff2?|eot|ttf|otf)$/i.test(assetInfo.name)) {
                        return `fonts/[name]-[hash].${ext}`;
                    }
                    
                    return `assets/[name]-[hash].${ext}`;
                },
                
                // Chunk file naming
                chunkFileNames: 'js/[name]-[hash].js',
                
                // Entry file naming
                entryFileNames: 'js/[name]-[hash].js',
            },
        },
        
        // Minification options (using esbuild for faster builds)
        minify: 'esbuild',
        
        // ESBuild options for minification
        esbuild: {
            drop: ['console', 'debugger'], // Remove console logs and debuggers in production
        },
        
        // Enable CSS code splitting
        cssCodeSplit: true,
        
        // Source map generation for debugging
        sourcemap: false, // Set to true for debugging in production
        
        // Asset inlining threshold (4kb)
        assetsInlineLimit: 4096,
        
        // Clear output directory before build
        emptyOutDir: true,
    },
    
    // CSS options
    css: {
        postcss: {
            plugins: [
                // Add autoprefixer if postcss.config.js doesn't exist
            ],
        },
        devSourcemap: true,
    },
    
    // Server options for development
    server: {
        host: 'localhost',
        port: 5173,
        strictPort: false,
        
        // HMR (Hot Module Replacement) options
        hmr: {
            host: 'localhost',
        },
        
        // Watch options
        watch: {
            usePolling: false,
        },
    },
    
    // Optimization options
    optimizeDeps: {
        include: [
            'axios',
        ],
    },
    
    // Base public path
    base: '/',
    
    // Define environment variables
    define: {
        __APP_VERSION__: JSON.stringify(process.env.npm_package_version),
    },
});
