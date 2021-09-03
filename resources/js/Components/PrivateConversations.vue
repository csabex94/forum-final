<template>
    <div class="flex flex-col w-full h-full">
        <div class="=w-full  h-full">
            <ul id="scrolling" class="flex flex-col relative w-full px-5 pb-5 " v-show="privateMessages && privateMessages.length > 0">
                <div v-if="lazyLoadingPrivateMessages === 'loading'" class="w-full absolute top-1 left-0 py-2 flex items-center justify-center">
                    <loading-spinner />
                </div>


                <private-conversation
                    :setCurrentMessageSeenBy="setCurrentMessageSeenBy"
                    :messagesLength="privateMessages.length"
                    :changeCurrentLeftSide="changeCurrentLeftSide"
                    :message="message"
                    v-for="(message, i) in privateMessages"
                    :key="i"
                    :messageIndex="i"
                    :ref="'privateConversationElRef'"
                    :scrollingToBottom="scrollingToBottom"
                    :sendingPrivateMessage="sendingPrivateMessage"
                />

                <div v-if="sendingPrivateMessage" class="w-full flex items-center justify-center py-2">
                    <loading-spinner />
                </div>
            </ul>
            <div v-if="privateMessages.length === 0" class="w-full h-full flex items-center justify-center">
                <img src="/privateill.svg" alt="..." class="illSize" style="opacity: 0.6;">
            </div>
        </div>
    </div>
</template>

<script>
import moment from 'moment';
import PrivateConversation from "@/Components/PrivateConversation";
import LoadingSpinner from "@/Components/LoadingSpinner";

export default {
    name: "PrivateConversations",
    components: {
        PrivateConversation,
        LoadingSpinner
    },
    props: {
        privateMessages: Array,
        getNewMessage: Function,
        changeCurrentLeftSide: Function,
        canReadMessages: Boolean,
        setCanReadMessages: Function,
        setReadedTeams: Function,
        setNewGroups: Function,
        setTotalUnreadPrivateMessages: Function,
        setTotalUnreadTopicConversations: Function,
        setCurrentMessageSeenBy: Function,
        totalUnreadPrivateMessages: Number,
        setMappedCurrentPrivateMessages: Function,
        lazyLoadingPrivateMessages: String,
        setLazyLoadingPrivateMessages: Function,
        sendingPrivateMessage: Boolean,
    },
    data() {
        return {
            currentPage: 1,
            canScroll: true
        }
    },
    methods: {
        scrollingToBottom(immediate) {
            const el = document.getElementById('scrolling');
            if (immediate && immediate === true) {
                el.scrollIntoView(false);
            } else {
                setTimeout(() => {
                    el.scrollIntoView(false);
                }, 400);
            }
        },
        calculateMessageTime(createdAt) {
            return moment(createdAt).fromNow();
        },
    },
    updated() {
        if (this.totalUnreadPrivateMessages > 0) {
            window.axios.post('/read-message-private').then(res => {
                this.setNewGroups(res.data.teams);
                this.setTotalUnreadPrivateMessages(res.data.totalUnreadPrivateMessages);
                this.setTotalUnreadTopicConversations(res.data.totalUnreadTopicConversations);
            });
        }
    },
    watch: {
        sendingPrivateMessage(newValue, oldValue) {
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
.illSize {
    width: 40%;
}
</style>
