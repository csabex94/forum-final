<template>
    <div class="flex flex-col w-full h-full">
        <div v-if="currentTopic" class="=w-full  px-3">
            <ul id="scrolling1" class="w-full relative" v-show="currentTopicConversations && currentTopicConversations.length > 0">

                <div v-if="lazyLoadingTopicConversations === 'loading'" class="w-full absolute top-1 left-0 py-2 flex items-center justify-center">
                    <loading-spinner />
                </div>

                <topic-conversation
                    v-for="(message, i) in currentTopicConversations"
                    :key="message.id"
                    :currentTopicConversationsLength="currentTopicConversations.length"
                    :openMessageSeenBy="openMessageSeenBy"
                    :conversationIndex="i"
                    :message="message"
                    :openningImage="openningImage"
                    :scrollingToBottom="scrollingToBottom"
                />
                <div v-if="sendingTopicConversation" class="w-full flex items-center justify-center py-2">
                    <loading-spinner />
                </div>
            </ul>

            <div v-if="currentTopicConversations.length === 0" class="w-full h-full flex items-center justify-center">
                <img src="/groupill.svg" alt="..." class="illSize mt-32" style="opacity: 0.6;">
            </div>

            <image-fullscreen
                v-if="openImage && imageToOpen"
                :imageToOpen="imageToOpen"
                :closingImage="closingImage"
            />

        </div>
        <div v-else class="text-xl text-gray-500 text-center w-full mt-32 text-indigo-400">
            This topic was deleted.
        </div>


        <div v-else class="w-full h-full flex flex-col items-center justify-center">
            <h3 class="text-2xl text-gray-500 text-center font-bold">Forum Secretari</h3>
            <img src="/emptyappill.svg" alt="..." style="opacity: .6;">
        </div>
    </div>
</template>

<script>
import moment from 'moment';
import JetDropdown from '@/Jetstream/Dropdown';
import JetDropdownLink from '@/Jetstream/DropdownLink';
import LoadingSpinner from "@/Components/LoadingSpinner";
import TopicConversation from "@/Components/TopicConversation";
import ImageFullscreen from "@/Components/ImageFullscreen";

export default {
    name: "TopicConversations",
    components: {
        JetDropdown,
        JetDropdownLink,
        LoadingSpinner,
        TopicConversation,
        ImageFullscreen
    },
    props: {
        message: Object,
        currentTopicConversations: Array,
        getNewTopicConversation: Function,
        setNewGroups: Function,
        changeCurrentLeftSide: Function,
        setTotalUnreadPrivateMessages: Function,
        setTotalUnreadTopicConversations: Function,
        currentTopic: Object,
        setCurrentMessageSeenBy: Function,
        totalUnreadTopicConversations: Number,
        lazyLoadingTopicConversations: String,
        sendingTopicConversation: Boolean
    },
    data() {
        return {
            showEditForm: false,
            openImage: false,
            imageToOpen: null,
            form: this.$inertia.form({
                _method: "put",
                title: "",
                description: "",
                topic_id: null
            }),
        }
    },
    methods: {
        linkify(inputText) {
            let replacedText, replacePattern1, replacePattern2, replacePattern3;

            //URLs starting with http://, https://, or ftp://
            replacePattern1 = /(\b(https?|ftp):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/gim;
            replacedText = inputText.replace(replacePattern1, '<a class="underline" href="$1" target="_blank">$1</a>');

            //URLs starting with "www." (without // before it, or it'd re-link the ones done above).
            replacePattern2 = /(^|[^\/])(www\.[\S]+(\b|$))/gim;
            replacedText = replacedText.replace(replacePattern2, '$1<a class="underline" href="http://$2" target="_blank">$2</a>');

            //Change email addresses to mailto:: links.
            replacePattern3 = /(([a-zA-Z0-9\-\_\.])+@[a-zA-Z\_]+?(\.[a-zA-Z]{2,6})+)/gim;
            replacedText = replacedText.replace(replacePattern3, '<a class="underline" href="mailto:$1">$1</a>');

            return replacedText;
        },
        getUserPhoto(message) {
            if (message.created_by.profile_photo_path) {
                return '/get-profile-photos/'+message.created_by.profile_photo_path;
            } else {
                return '/user.png';
            }
        },
        scrollingToBottom(immediate) {
            const el = document.getElementById('scrolling1');
            if (el) {
                if (immediate) {
                    el.scrollIntoView(false);
                } else {
                    setTimeout(() => {
                        el.scrollIntoView(false);
                    }, 100);
                }
            }
        },
        calculateMessageTime(createdAt) {
            return moment(createdAt).fromNow();
        },
        showEditTopicForm() {
            this.form.title = this.$page.props.currentTopics[0].title;
            this.form.description = this.$page.props.currentTopics[0].description;
            this.showEditForm = true;
        },
        closeEditTopicForm() {
            this.form.reset();
            this.showEditForm = false;
        },
        updateTopic(topicId) {
            this.form.topic_id = topicId;
            this.form.put(route('topic.update'
            ), {
                preserveState: true,
                preserveScroll: true,
                onSuccess: () => {
                    this.showEditForm = false;
                    this.form.reset();
                }
            })
        },
        openningImage(img) {
            this.openImage = true;
            this.imageToOpen = img;
            // this.changeCurrentLeftSide('');
        },
        closingImage() {
            this.openImage = false;
            this.imageToOpen = null;
        },
        openMessageSeenBy(message) {
            this.changeCurrentLeftSide('messages-seen-by');
            this.setCurrentMessageSeenBy(message);
        },
        lastMessage() {
            console.log(this.currentTopicConversations[this.currentTopicConversations.length - 1])
        },
        deleteTopicMessage(message) {
            message.is_deleted = 1;
            window.axios.post(`/delete-topic-message?tmid=${message.id}`).then(res => res.data);
        }
    },
    mounted() {
        this.scrollingToBottom();
        if (this.totalUnreadTopicConversations > 0 && this.currentTopic) {
            window.axios.post(`/read-message-topic?tid=${this.currentTopic.id}`).then(res => {
                this.setNewGroups(res.data.teams);
                this.setTotalUnreadPrivateMessages(res.data.totalUnreadPrivateMessages);
                this.setTotalUnreadTopicConversations(res.data.totalUnreadTopicConversations);
            })
        }
    },
    updated() {
        if (this.currentTopic) {
            window.axios.post(`/read-message-topic?tid=${this.currentTopic.id}`).then(res => {
                this.setNewGroups(res.data.teams);
                this.setTotalUnreadPrivateMessages(res.data.totalUnreadPrivateMessages);
                this.setTotalUnreadTopicConversations(res.data.totalUnreadTopicConversations);
            });
        }
    },
    watch: {
        sendingTopicConversation(newValue, oldValue) {
            if (newValue && newValue === true) {
                this.scrollingToBottom(true);
            }
        }
    }
}
</script>

<style scoped>
    .contrast {
        filter: grayscale(1) invert(1);
    }
    .openedImg {
        height: auto;
        max-height: 100%;
        width: auto;
        object-position: center;
        object-fit: cover;
        z-index: 100;
    }
    .illSize {
        width: 60%;
    }
</style>
