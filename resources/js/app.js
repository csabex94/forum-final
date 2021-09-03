require('./bootstrap');

// Import modules...
import { createApp, h } from 'vue';
import { App as InertiaApp, plugin as InertiaPlugin } from '@inertiajs/inertia-vue3';
// import { InertiaProgress } from '@inertiajs/progress';
const el = document.getElementById('app');

const app = createApp({
    render: () =>
        h(InertiaApp, {
            initialPage: JSON.parse(el.dataset.page),
            resolveComponent: (name) => require(`./Pages/${name}`).default
        }),
})
    .mixin({ methods:
        {
            route,
            error(field, errorBag = 'default') {
                if (!this.$page.errors.hasOwnProperty(errorBag)) {
                    return null;
                }
                if (this.$page.errors[errorBag].hasOwnProperty(field)) {
                    return this.$page.errors[errorBag][field][0];
                }
                return null;
            }
        }
    })
    .use(InertiaPlugin)
app.mount(el);

// InertiaProgress.init({ color: '#4B5563' });


