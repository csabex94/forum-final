<template>
    <div >
        <div class="flex items-center justify-between">
            <span class="font-light text-gray-600">{{ getFormattedDate(topicData.created_at) }}</span>
            <span class="flex flex-row">
                    <div class="mr-4">
                        <img src="/user.png" class="w-8 h-8 bg-gray-700 rounded-full contrast" alt="">
                    </div>
                    <div class="flex flex-col justify-center">
                        <h2 class="text-sm text-gray-500">{{ topicData.created_by.name }}</h2>
                    </div>
                </span>
        </div>
        <div class="mt-2">
            <span class="text-2xl font-bold text-gray-700 hover:text-gray-600">{{ topicData.title }}</span>
            <p class="mt-2 text-gray-600">
                {{ topicData.description }}
            </p>
        </div>
        <div class="flex items-center justify-between mt-4">
            <inertia-link  :href="route('home', { topicId: topicData.id, tn: topicData.title })" :only="['topicConversations', 'currentTopicConversations', 'middleSection', 'topicName']" class="inline-flex items-center mt-4 text-indigo-500">
                <button type="button" class="px-5 py-2 text-sm text-white bg-blue-500 rounded-md focus:outline-none hover:bg-blue-600 hover:shadow-lg">
                    Messages
                </button>
            </inertia-link>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            topicData: Object
        },
         methods: {
            getFormattedDate(date) {
                return moment(date).format("DD MMM YYYY H:m");
            }
        },
    }
</script>

<style scoped>
.contrast {
        filter: grayscale(1) invert(1);
    }
</style>