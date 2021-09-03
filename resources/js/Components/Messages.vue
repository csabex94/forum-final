<template>
    <div class="flex flex-col flex-auto h-full px-5">
        <div v-if="!currentTopics || currentTopics.length === 0 && $page.props.user.current_team.name !== 'not-showing'" class="flex justify-center w-full mt-10 text-xl font-semibold text-gray-600">
            No Topics in:&nbsp; <span class="text-blue-400">{{ $page.props.user.current_team.name }}</span>&nbsp;' s group.
        </div>

        <div v-if="!currentTopics || currentTopics.length === 0 && $page.props.user.current_team.name !== 'not-showing'" class="w-full h-full flex items-center justify-center">
            <img src="/topicill.svg" alt="..." class="illSize" style="opacity: 0.6;">
        </div>

        <div v-if="currentTopics" >

            <jet-dialog-modal :show="showDeleteTopicModal" @close="closeDeleteTopicModal">
                <template #content>
                    <div class="justify-center flex-auto p-5 text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="flex items-center w-6 h-6 mx-auto -m-1 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <svg xmlns="http://www.w3.org/2000/svg" class="flex items-center w-16 h-16 mx-auto text-red-500" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        <h2 class="py-4 text-xl font-bold ">Are you sure?</h2>
                        <p class="px-8 text-sm text-gray-500">Do you really want to remove {{ topic.title }} from your group? <br>(All messages and files contained will be deleted.)</p>
                    </div>
                </template>
                <template #footer>
                    <div class="p-3 mt-2 space-x-4 text-center md:block">
                        <button @click="closeDeleteTopicModal" class="px-5 py-2 mb-2 text-sm font-medium tracking-wider text-gray-600 bg-white border rounded-full shadow-sm md:mb-0 hover:shadow-lg hover:bg-gray-100">
                            Cancel
                        </button>
                        <button type="button" @click.prevent="deleteTopic(topic, i)" class="px-5 py-2 mb-2 text-sm font-medium tracking-wider text-white bg-red-500 border border-red-500 rounded-full shadow-sm md:mb-0 hover:shadow-lg hover:bg-red-600">Delete</button>
                    </div>
                </template>
            </jet-dialog-modal>

            <div class="relative" v-for="(topic, i) in currentTopics" :key="i">
                    <div class="px-10 py-6 my-4 relative bg-white rounded-lg shadow-md ">
                        <div class="flex items-center justify-end">
                            <span class="flex-1 font-light font-semibold text-xs text-gray-600">{{ getFormattedDate(topic.created_at) }}</span>

                            <button v-if="topic.user_id === $page.props.user.id" @click="editTopic(topic)" type="button" class="p-2 mr-2 has-tooltip text-gray-400 rounded-full hover:text-gray-600 hover:bg-gray-300 focus:outline-none focus:ring">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                </svg>
                                <span class='tooltip z-50 text-xs rounded-lg shadow-lg  p-2 text-white bg-gray-600 -mt-16'>Edit Topic
                                   <svg class="z-40 w-4 h-4 absolute left-1 -bottom-1.5 text-gray-600" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M17.707 9.293a1 1 0 010 1.414l-7 7a1 1 0 01-1.414 0l-7-7A.997.997 0 012 10V5a3 3 0 013-3h5c.256 0 .512.098.707.293l7 7zM5 6a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path></svg>
                                </span>
                            </button>

                            <button v-if="topic.user_id === $page.props.user.id" @click="openDeleteTopicModal(topic)" type="button" class="p-2 has-tooltip mr-2 text-gray-400 rounded-full hover:text-gray-600 hover:bg-gray-300 focus:outline-none focus:ring">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                <span class='tooltip z-50 text-xs rounded-lg shadow-lg  p-2 text-white bg-red-500 -mt-16'>Delete
                                   <svg class="z-40 w-4 h-4 absolute left-1 -bottom-1.5 text-red-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M17.707 9.293a1 1 0 010 1.414l-7 7a1 1 0 01-1.414 0l-7-7A.997.997 0 012 10V5a3 3 0 013-3h5c.256 0 .512.098.707.293l7 7zM5 6a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path></svg>
                                </span>
                            </button>

                            <span v-if="topic.userStatus === 'not-seen'" class="bg-indigo-500 text-xs rounded-lg text-white px-2 py-1">New</span>

                            <span
                                    v-if="topic.topic_conversations && topic.topic_conversations.length > 0"
                                    class="absolute px-2 py-1 text-xs text-white bg-blue-400 rounded-full -top-2 left-5"
                                >
                            {{ topic.topic_conversations.length }}
                        </span>

                        </div>
                        <div class="mt-2">
                            <span class="text-xl font-bold text-gray-700 hover:text-gray-600" >{{ topic.title }}</span>
                            <p class="mt-2 text-gray-600">{{ topic.description }}</p>
                        </div>
                        <div class="flex items-center justify-between mt-4">
                            <inertia-link
                                @click="setLoadingMiddleSection(true)"
                                :href="route('home', { topicId: topic.id, tn: topic.title, uso: topic.userStatus })"
                                :only="['topicConversations', 'currentTopicConversations', 'middleSection', 'topicName', 'currentTopic']"
                                class="text-base flex items-center text-bold text-blue-400 hover:underline"
                            >Enter Topic
                                <svg class="w-4 h-4 ml-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                            </inertia-link>
                            <div>
                                <span class="flex items-center">
                                    <img
                                        class="hidden object-cover w-6 h-6 mr-2 rounded-full sm:block"
                                        :class="!topic.created_by.profile_photo_path && 'contrast bg-gray-900'"
                                        alt="avatar"
                                        :src="topic.created_by.profile_photo_path ? '/get-profile-photos/'+topic.created_by.profile_photo_path : '/user.png'"
                                    >
                                    <h1 class="font-semibold text-xs text-blue-500">{{ topic.created_by.name }}</h1>
                                </span>
                            </div>
                        </div>
                    </div>

            </div>

            <div v-if="loadingTopicsPaginate" class="absolute bottom-0 w-3/4 paginateLoadingSpinnerPosition flex items-center justify-center">
                <div class="w-20">
                    <loading-spinner />
                </div>
            </div>

        </div>
    </div>
</template>

<script>
import moment from 'moment';
import JetDialogModal from '@/Jetstream/DialogModal';
import LoadingSpinner from "@/Components/LoadingSpinner";

export default {
    name: "Messages",
    props: {
        currentTopics: Array,
        changeCurrentMiddleSection: Function,
        setCurrentTopic: Function,
        setMappedCurrentTopics: Function,
        setLoadingMiddleSection: Function,
        editTopic: Function,
        loadingTopicsPaginate: Boolean,
        topicConversationScroll: Function
    },
    components: {
        JetDialogModal,
        LoadingSpinner
    },
    data() {
        return {
            showDeleteTopicModal: false,
            topic: null,
        }
    },
    methods: {
        getFormattedDate(date) {
            return moment(date).format("DD MMM YYYY H:m");
        },
        deleteTopic() {
            this.$inertia.visit(route('delete.topic', { topicId: this.topic.id }), {
                method: 'post',
                only: ['currentTopics', 'middleSection', 'rightSide'],
                preserveState: true,
                onSuccess: (res) => {
                    this.closeDeleteTopicModal();
                    this.setMappedCurrentTopics(res.props.currentTopics);
                }
            })
        },
        openDeleteTopicModal(topic) {
            this.showDeleteTopicModal = true;
            this.topic = topic;
        },
        closeDeleteTopicModal() {
            this.showDeleteTopicModal = false;
            this.topic = null;
        },
        enterTopicConversation(topic) {
            this.setLoadingMiddleSection(true);
            this.$inertia.visit(route('home'), {
                method: 'get',
                data: { topicId: topic.id, tn: topic.title, uso: topic.userStatus },
                only: ['topicConversations', 'currentTopicConversations', 'middleSection', 'topicName', 'currentTopic'],
                preserveScroll: true,
                onSuccess: () => {
                    this.setLoadingMiddleSection(false);
                    this.topicConversationScroll();
                }
            })
        }
    },
}
</script>

<style scoped>
 .contrast {
        filter: grayscale(1) invert(1);
 }
.paginateLoadingSpinnerPosition {
    left: 50%;
    transform: translateX(-50%);
}
</style>
