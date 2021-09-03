<template>
    <li
        v-if="!deletedPermanently"
         class="flex mt-1 space-x-3"
        :class="$page.props.user.id === message.created_by.id ? 'justify-end'+' '+lastMessage(conversationIndex) : 'justify-start'+' '+lastMessage(conversationIndex)"
    >

        <div class="flex items-start">

            <img
                v-if="$page.props.user.id !== message.created_by.id" :src="getUserPhoto(message)"
                :class="!message.created_by.profile_photo_path && 'contrast bg-gray-900'"
                class="w-8 h-8 mr-2 object-cover rounded-full"
                alt="User img"
            >

            <div :class="$page.props.user.id === message.created_by.id ? 'flex flex-col items-end' : 'flex flex-col items-start'">
                <div
                    :class="$page.props.user.id === message.created_by.id ? 'bg-blue-400 text-white rounded-l-lg rounded-br-lg' : 'bg-gray-700 text-white rounded-r-lg rounded-bl-lg'"
                    class="max-w-3xl p-3 mt-3 maxHeight"
                >
                    <div class="flex items-center justify-between">
                                <span
                                    :class="$page.props.user.id === message.created_by.id ? 'text-gray-200 justify-end' : 'text-gray-500 justify-start'"
                                    class="flex items-center mt-1 mb-2 mr-1 text-xs text-gray-500">
                                    <span v-if="$page.props.user.id === message.created_by.id">you</span>
                                    <span class="text-white" v-else>{{ message.created_by.name }}</span>
                                </span>
                        <jet-dropdown v-if="!message.temp">
                            <template #trigger>
                                <button
                                    v-if="message.created_by.id === $page.props.user.id"
                                    type="button"
                                    class="p-1 ml-2 text-white rounded-full hover:text-gray-600 hover:bg-gray-200 focus:outline-none"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z" />
                                    </svg>
                                </button>
                            </template>
                            <template #content>
                                <jet-dropdown-link v-if="!message.is_deleted" as="button" @click="openMessageSeenBy(message)">
                                    Seen
                                </jet-dropdown-link>
                                <jet-dropdown-link v-if="message && message.is_deleted" as="button" @click="deleteTopicMessagePermanently(message)">
                                    Delete Permanently
                                </jet-dropdown-link>
                                <jet-dropdown-link v-if="message &&!message.is_deleted" as="button" @click="deleteTopicMessage(message)">
                                    Delete
                                </jet-dropdown-link>
                            </template>
                        </jet-dropdown>
                    </div>
                    <p v-if="message.is_deleted" class="text-sm">This message was removed.</p>
                    <p v-if="!message.is_deleted && message.message" v-html="linkify(message.message)" class="text-sm"></p>
                    <div v-if="message.files && message.files.length > 0">
                        <div v-for="file in message.files" :key="file.id">
                            <div v-if="!message.is_deleted">
                                <div v-if="file.extension === 'mp4'" class="bg-gray-900">
                                    <video style="width: 20rem; height: 20rem;" controls preload="metadata">
                                        <source :src="`/video-get/${file.filename}`" />
                                    </video>
                                </div>
                                <div v-if="file.extension === 'jfif' || file.extension === 'jpg' || file.extension === 'jpeg' || file.extension === 'png'">
                                    <img
                                        v-if="!messageLoading"
                                        @click="openningImage(file)"
                                        :ref="`convImg-${message.id}`"
                                        :src="`/image-get/${file.filename}`" alt="Img" class="rounded-md cursor-pointer imageSize"
                                    />
                                    <div v-if="messageLoading" style="height: 150px; width: 150px;" class="flex items-center justify-center">
                                        <white-loading-spinner />
                                    </div>
                                    <span class="text-xs text-white">{{ file.original_filename }}</span>
                                </div>

                                <a v-else :href="'/download-topic-file/' + file.filename" target="_blank" class="flex flex-col justify-center mt-3 text-xs font-semibold">
                                    <img v-if="file.extension === 'pdf'" src="/pdf.png" alt="pdf" class="w-8 h-8 mr-2" />
                                    <img v-if="file.extension === 'xlsx'" src="/excel.png" alt="Excell" class="w-8 h-8 mr-2" />
                                    <img v-if="file.extension === 'doc'" src="/word.png" alt="Word" class="w-8 h-8 mr-2" />

                                    <img v-if="file.extension === 'rtf' || file.extension === 'txt'" src="/doc.png" alt="Document" class="w-8 h-8 mr-2" />
                                    <span class="text-xs text-white">{{ file.original_filename }}</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <span class="mx-2 mt-1 text-xs leading-none text-gray-800">{{ calculateMessageTime(message.created_at) }}</span>
            </div>

            <img
                v-if="$page.props.user.id === message.created_by.id" :src="getUserPhoto(message)"
                :class="!message.created_by.profile_photo_path && 'contrast bg-gray-900'"
                class="w-8 h-8 ml-2 object-cover rounded-full"
                alt="User img"
            >

        </div>

    </li>
</template>

<script>
import moment from "moment";
import JetDropdown from '@/Jetstream/Dropdown';
import JetDropdownLink from '@/Jetstream/DropdownLink';
import WhiteLoadingSpinner from "@/Components/WhiteLoadingSpinner";

export default {
    name: "TopicConversation",
    components: {
        JetDropdownLink,
        JetDropdown,
        WhiteLoadingSpinner
    },
    props: {
        message: Object,
        openningImage: Function,
        openMessageSeenBy: Function,
        currentTopicConversationsLength: Number,
        conversationIndex: Number,
        scrollingToBottom: Function
    },
    data() {
        return {
            messageLoading: true,
            deletedPermanently: false
        }
    },
    methods: {
        linkify(inputText) {
            let replacedText, replacePattern1, replacePattern2, replacePattern3, replacePattern4;

            //URLs starting with http://, https://, or ftp://
            replacePattern1 = /(\b(https?|ftp):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/gim;
            replacedText = inputText.replace(replacePattern1, '<a class="underline" href="$1" target="_blank">$1</a>');

            //URLs starting with "www." (without // before it, or it'd re-link the ones done above).
            replacePattern2 = /(^|[^\/])(www\.[\S]+(\b|$))/gim;
            replacedText = replacedText.replace(replacePattern2, '$1<a class="underline" href="http://$2" target="_blank">$2</a>');

            //Change email addresses to mailto:: links.
            replacePattern3 = /(([a-zA-Z0-9\-\_\.])+@[a-zA-Z\_]+?(\.[a-zA-Z]{2,6})+)/gim;
            replacedText = replacedText.replace(replacePattern3, '<a class="underline" href="mailto:$1">$1</a>');

            replacePattern4 = /(([a-zA-Z0-9\-\_\.])+giphy[a-zA-Z\_]+?(\.[a-zA-Z]{2,6})+)/gim;
            if (inputText.match(new RegExp('https://media.giphy.com/media/'))) {
                replacedText = `<img src=${inputText} style="width: 300px; height: 250px;" alt="..." />`
            }

            // Detect video
            if (inputText.match(/(youtube|youtu)\.(com|be)\/((watch\?v=([-\w]+))|(video\/([-\w]+))|(projects\/([-\w]+)\/([-\w]+))|([-\w]+))/)) {
                replacedText = inputText;
            }

            // Detect video
            if (inputText.match(/(vimeo)\.(com|be)\/((watch\?v=([-\w]+))|(video\/([-\w]+))|(projects\/([-\w]+)\/([-\w]+))|([-\w]+))/)) {
                replacedText = inputText;
            }

            // Detect video
            if (inputText.match(/(dailymotion)\.(com|be)\/((watch\?v=([-\w]+))|(video\/([-\w]+))|(projects\/([-\w]+)\/([-\w]+))|([-\w]+))/)) {
                replacedText = `<div id="dailymotionWrapper">${inputText}</div>`;
            }

            return replacedText;

        },
        getUserPhoto(message) {
            if (message.created_by.profile_photo_path) {
                return '/get-profile-photos/'+message.created_by.profile_photo_path;
            } else {
                return '/user.png';
            }
        },
        calculateMessageTime(createdAt) {
            return moment(createdAt).fromNow();
        },
        deleteTopicMessage(message) {
            message.is_deleted = 1;
            window.axios.post(`/delete-topic-message?tmid=${message.id}`).then(res => res.data);
        },
        deleteTopicMessagePermanently(message) {
            window.axios.delete(`/delete-topic-conversation-permanently?tmid=${message.id}`).then(res => {
                this.deletedPermanently = true;
            })
        },
        lastMessage(index) {
            if (index === this.currentTopicConversationsLength - 1) {
                return 'pb-1';
            } else {
                return '';
            }
        },
    },
    created() {
        const images = this.message.files.map(file => {
            if (file.extension === 'jpeg' || file.extension === 'jpg' || file.extension === 'png' || file.extension === 'jfif') {
                return new Promise((resolve, reject) => {
                    const img = new Image();
                    img.src = '/image-get/'+file.filename;
                    img.onload = resolve;
                    img.onerror = reject;
                });
            }
        });
        Promise.all(images).then(() => {
            this.messageLoading = false;
        }).catch(error => {
            console.log({customError: error})
        })
    },
    mounted() {
        const embedded = document.querySelectorAll('iframe');
        if (embedded && embedded.length > 0) {
            embedded.forEach(embed => {
                embed.width = "500";
                embed.height = "315";
            });
        }
        const dailymotionWrapper = document.getElementById('dailymotionWrapper');
        if (dailymotionWrapper && dailymotionWrapper.firstChild) {
            dailymotionWrapper.firstChild.style.width = "25rem";
            dailymotionWrapper.firstChild.style.height = "20rem";
        }
    }
}
</script>

<style scoped>
.imageSize {
    width: 150px;
    height: 150px;
    object-fit: contain;
    object-position: center;
}
</style>
