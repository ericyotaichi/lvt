import '../css/app.css';
import './bootstrap';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import { Ziggy } from './ziggy';

// 确保 Ziggy 路由被正确设置
if (typeof window !== 'undefined' && window.Ziggy) {
    Object.assign(Ziggy.routes, window.Ziggy.routes);
}

// 修复 Ziggy URL 以匹配当前窗口的 origin（处理端口问题）
if (typeof window !== 'undefined') {
    const currentOrigin = window.location.origin;
    if (Ziggy.url !== currentOrigin) {
        Ziggy.url = currentOrigin;
    }
}

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue, Ziggy)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
