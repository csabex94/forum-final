<template>
    <li
        v-if="!deletedPermanently"
        :class="$page.props.user.id === message.created_by.id ? 'justify-end'+' '+lastMessage() : 'justify-start'+' '+lastMessage()"
        :id="`message-${messageIndex}`"
    >
        <div :class="$page.props.user.id === message.created_by.id ? 'flex flex-col items-end' : 'flex flex-col items-start'">
            <div class="flex items-start">
                <img
                    v-if="$page.props.user.id !== message.created_by.id" :src="getUserPhoto()"
                    :class="!message.created_by.profile_photo_path && 'contrast bg-gray-900'"
                    class="w-8 h-8 mr-2 object-cover rounded-full"
                    alt="User img"
                >
                <div
                    :class="$page.props.user.id === message.created_by.id ? 'bg-blue-400 text-white rounded-l-lg rounded-br-lg' : 'bg-gray-700 rounded-r-lg rounded-bl-lg'"
                    class="max-w-2xl p-3 mt-3 maxHeight shadow"
                >
                    <div class="flex items-center justify-between">
                    <span
                        :class="$page.props.user.id === message.created_by.id ? 'text-gray-100 justify-end' : 'text-gray-200 justify-start'"
                        class="flex items-center mt-1 mb-2 mr-1 text-xs font-semibold text-gray-500">
                                <span v-if="$page.props.user.id === message.created_by.id">you</span>
                                <span v-else>{{ message.created_by.name }}</span>
                            </span>

                        <jet-dropdown>
                            <template #trigger>
                                <button
                                    v-if="message.created_by.id === $page.props.user.id"
                                    type="button"
                                    class="p-1 ml-2 text-white rounded-full hover:text-gray-600 hover:bg-gray-200 focus:outline-none"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z" />
                                    </svg>
                                </button>
                            </template>
                            <template #content>
                                <jet-dropdown-link as="button" v-if="!message.is_deleted" @click="openMessageSeenBy">
                                    Seen
                                </jet-dropdown-link>
                                <jet-dropdown-link v-if="message && message.is_deleted" @click="deleteMessagePermanently" as="button">
                                    Delete Permanently
                                </jet-dropdown-link>
                                <jet-dropdown-link v-if="message && !message.is_deleted" @click="deleteMessage" as="button">
                                    Delete
                                </jet-dropdown-link>
                            </template>
                        </jet-dropdown>
                    </div>
                    <p v-html="linkify(message.message)" v-if="message.message && !message.is_deleted" class="text-sm flex flex-col max-w-2xl text-white"></p>

                    <div v-if="linkPreview" class="flex flex-col">
                        <div class="flex items-start justify-between">
                            <div v-if="linkPreview.title">{{ linkPreview.title }}</div>
                            <div v-if="linkPreview.image">
                                <img :src="linkPreview.image" alt="..." class="w-20 h-20">
                            </div>
                        </div>
                        <div v-if="linkPreview.description">{{ linkPreview.description }}</div>
                    </div>

                    <p v-if="message.is_deleted" class="text-sm flex max-w-2xl text-white">This message was deleted.</p>


                    <div v-if="!message.is_deleted && message.files && message.files.length > 0">
                        <div v-for="file in message.files" :key="file.id">
                           <div v-if="!message.is_delete">
                               <div v-if="file.extension === 'mp4'" class="bg-gray-900">
                                   <video style="width: 20rem; height: 20rem;" controls preload="metadata">
                                       <source :src="`/video-get/${file.filename}`" />
                                   </video>
                               </div>
                               <div v-if="file.extension === 'jfif' || file.extension === 'jpg' || file.extension === 'jpeg' || file.extension === 'png'">
                                   <img
                                       v-if="!messageLoading"
                                       @click="openningImage(file)"
                                       :id="`convImg-${message.id}`"
                                       :src="`/image-get/${file.filename}`" alt="Img" class="imageSize cursor-pointer rounded-md"
                                   />
                                   <div v-if="messageLoading" style="height: 150px; width: 150px;" class="flex items-center justify-center">
                                       <white-loading-spinner />
                                   </div>
                                   <span class="text-white text-xs">{{ file.original_filename }}</span>
                               </div>

                               <a v-else :href="'/download/' + file.filename" target="_blank" class="flex flex-col justify-center mt-3 text-xs font-semibold">
                                   <img v-if="file.extension === 'pdf'" src="/pdf.png" alt="pdf" class="w-8 h-8 mr-2" />
                                   <img v-if="file.extension === 'xlsx'" src="/excel.png" alt="Excell" class="w-8 h-8 mr-2" />
                                   <img v-if="file.extension === 'doc'" src="/word.png" alt="Word" class="w-8 h-8 mr-2" />

                                   <img v-if="file.extension === 'rtf' || file.extension === 'txt'" src="/doc.png" alt="Document" class="w-8 h-8 mr-2" />
                                   <span class="text-white text-xs">{{ file.original_filename }}</span>
                               </a>
                           </div>
                        </div>
                    </div>
                </div>
                <img
                    v-if="$page.props.user.id === message.created_by.id"
                    :src="getUserPhoto()"
                    class="w-8 h-8 object-cover ml-2 rounded-full"
                    :class="!message.created_by.profile_photo_path && 'contrast bg-gray-900'"
                    alt="User img">
            </div>
            <span class="mx-16 mt-1 text-xs leading-none text-gray-800">{{ calculateMessageTime(message.created_at) }}</span>
        </div>

        <image-fullscreen
            v-if="openImage && imageToOpen"
            :closingImage="closingImage"
            :imageToOpen="imageToOpen" />
    </li>
</template>

<script>
import moment from "moment";
import JetDropdown from '@/Jetstream/Dropdown';
import JetDropdownLink from '@/Jetstream/DropdownLink';
import WhiteLoadingSpinner from "@/Components/WhiteLoadingSpinner";
import ImageFullscreen from "@/Components/ImageFullscreen";
import LinkPrevue from 'link-prevue'
import LoadingSpinner from "@/Components/LoadingSpinner";

export default {
    name: "PrivateConversation",
    components: {
        LoadingSpinner,
        JetDropdown,
        JetDropdownLink,
        WhiteLoadingSpinner,
        LinkPrevue,
        ImageFullscreen
    },
    props: {
        message: Object,
        changeCurrentLeftSide: Function,
        key: Number,
        messagesLength: Number,
        setCurrentMessageSeenBy: Function,
        messageIndex: Number,
        messageImg: null,
        scrollingToBottom: Function,
        sendingPrivateMessage: Boolean,
    },
    data() {
      return {
          openImage: false,
          imageToOpen: null,
          messageLoading: true,
          linkPreview: null,
          fetchText: null,
          canFetch: true,
          deletedPermanently: false
      }
    },
    methods: {
        linkify(inputText) {
            let replacedText, replacePattern1, replacePattern2, replacePattern3, replacePattern4;
            let youtubePattern = /(youtube|youtu)\.(com|be)\/((watch\?v=([-\w]+))|(video\/([-\w]+))|(projects\/([-\w]+)\/([-\w]+))|([-\w]+))/;
            let dailymotionPattern = /(dailymotion)\.(com|be)\/((watch\?v=([-\w]+))|(video\/([-\w]+))|(projects\/([-\w]+)\/([-\w]+))|([-\w]+))/;
            let vimeoPattern = /(vimeo)\.(com|be)\/((watch\?v=([-\w]+))|(video\/([-\w]+))|(projects\/([-\w]+)\/([-\w]+))|([-\w]+))/;
            let giphyPattern = 'https://media.giphy.com/media/';

            //URLs starting with http://, https://, or ftp://
            replacePattern1 = /(\b(https?|ftp):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/gim;

            if (inputText.match(replacePattern1) && !inputText.match(youtubePattern) && !inputText.match(dailymotionPattern) && !inputText.match(vimeoPattern) && !inputText.match(new RegExp(giphyPattern))) {
                if (!this.fetchText && !this.linkPreview && this.canFetch) {
                    this.fetchText = inputText;
                }
            }

            replacedText = inputText.replace(replacePattern1, '<a class="underline" href="$1" target="_blank">$1</a>');



            //URLs starting with "www." (without // before it, or it'd re-link the ones done above).
            replacePattern2 = /(^|[^\/])(www\.[\S]+(\b|$))/gim;
            replacedText = replacedText.replace(replacePattern2, '$1<a class="underline" href="http://$2" target="_blank">$2</a>');

            //Change email addresses to mailto:: links.
            replacePattern3 = /(([a-zA-Z0-9\-\_\.])+@[a-zA-Z\_]+?(\.[a-zA-Z]{2,6})+)/gim;
            replacedText = replacedText.replace(replacePattern3, '<a class="underline" href="mailto:$1">$1</a>');

            replacePattern4 = /(([a-zA-Z0-9\-\_\.])+giphy[a-zA-Z\_]+?(\.[a-zA-Z]{2,6})+)/gim;
            if (inputText.match(new RegExp(giphyPattern))) {
                replacedText = `<img src=${inputText} style="width: 300px; height: 250px;" alt="..." />`
            }
            // Detect video - youtube embed
            if (inputText.match(youtubePattern)) {
                replacedText = inputText;
            }
            // Detect video - vimeo embedd
            if (inputText.match(vimeoPattern)) {
                replacedText = inputText;
            }
            // Detect video - dailymotion embedd
            if (inputText.match(dailymotionPattern)) {
                replacedText = `<div id="dailymotionWrapper">${inputText}</div>`;
            }
            return replacedText;
        },
        getUserPhoto() {
            if (this.message.created_by.profile_photo_path) {
                return '/get-profile-photos/'+this.message.created_by.profile_photo_path;
            } else {
                return '/user.png'
            }
        },
        calculateMessageTime(createdAt) {
            return moment(createdAt).fromNow();
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
        openMessageSeenBy() {
            this.changeCurrentLeftSide('messages-seen-by');
            this.setCurrentMessageSeenBy(this.message);
        },
        lastMessage() {
            if (this.messageIndex === this.messagesLength - 1) {
                return 'mb-20';
            } else {
                return '';
            }
        },
        deleteMessage() {
            this.message.is_deleted = 1;
            window.axios.post(`/delete-private-message?pmid=${this.message.id}`).then(res => res.data);
        },
        deleteMessagePermanently() {
            window.axios.delete(`/delete-private-message-permanently?pmid=${this.message.id}`).then(res => {
                this.deletedPermanently = true;
            })
        }
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
        const dailymotionWrapper = document.querySelectorAll('#dailymotionWrapper');
        if (dailymotionWrapper && dailymotionWrapper.length > 0) {
            dailymotionWrapper.forEach(wrapper => {
                if (wrapper && wrapper.firstChild) {
                    wrapper.firstChild.style.width = "25rem";
                    wrapper.firstChild.style.height = "20rem";
                }
            })
        }
    },
    watch: {
        fetchText(newValue, oldValue) {
            if (this.canFetch && newValue) {
                window.axios.get(newValue, { headers: {"Access-Control-Allow-Origin": "*"} }).then(res => {
                    this.linkPreview = res.data;
                    this.fetchText = null;
                    this.canFetch = false;
                });
            }
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
    .gifSize {
        width: 200px;
        max-height: 200px;
    }
</style>
