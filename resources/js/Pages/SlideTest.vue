<template>
    <div class="flex h-screen overflow-x-hidden">
        <div class="w-full h-full bg-red-500">
            <button type="button" @click="open">open</button>
        </div>

        <div :class="show ? 'slideIn kek h-full bg-blue-500' : 'slideOut'">
            <button type="button" @click="close">close</button>
        </div>
    
    </div>
</template>

<script>
    export default {
        data() {
            return {
                show: false
            }
        },
        methods: {
            open() {
                this.show = true
            },
            close() {
                this.show = false;
            }
        }
    }
</script>

<style  scoped>
    .kek {
        width: 360px;
        transition: .7s;
    }
    .slideIn {
        transform: translateX(0);
    }
    .slideOut {
        transform: translateX(100%);
        width: 0;
        transition: .7s;
    }
</style>