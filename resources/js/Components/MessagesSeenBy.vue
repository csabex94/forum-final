<template>
    <div class="h-screen p-2 overflow-y-auto bg-white rounded-r-md customScroll">
        <header class="flex flex-row items-center justify-between">
            <h4 class="text-base font-bold text-gray-500">Seen By</h4>
            <button @click="changeCurrentLeftSide('')" type="button" class="p-2 ml-2 text-gray-400 rounded-full hover:text-gray-600 hover:bg-gray-300 focus:outline-none focus:ring">
                <svg class="w-6 h-6 fill-current" viewBox="0 0 20 20">
                    <path d="M15.898,4.045c-0.271-0.272-0.713-0.272-0.986,0l-4.71,4.711L5.493,4.045c-0.272-0.272-0.714-0.272-0.986,0s-0.272,0.714,0,0.986l4.709,4.711l-4.71,4.711c-0.272,0.271-0.272,0.713,0,0.986c0.136,0.136,0.314,0.203,0.492,0.203c0.179,0,0.357-0.067,0.493-0.203l4.711-4.711l4.71,4.711c0.137,0.136,0.314,0.203,0.494,0.203c0.178,0,0.355-0.067,0.492-0.203c0.273-0.273,0.273-0.715,0-0.986l-4.711-4.711l4.711-4.711C16.172,4.759,16.172,4.317,15.898,4.045z"></path>
                </svg>
            </button>
        </header>
        <div class="px-6">
            <div
                class="w-full shadow-md bg-blue-400 p-3 mt-10 rounded-md"
            >
                <p v-if="currentMessageSeenBy.message" v-html="linkify(currentMessageSeenBy.message)" class="text-sm flex text-white"></p>
                <div v-if="currentMessageSeenBy.files && currentMessageSeenBy.files.length > 0">
                    <div v-for="file in currentMessageSeenBy.files" :key="file.id">
                        <div v-if="file.extension === 'mp4'" class="bg-gray-900">
                            <video style="width: 20rem; height: 20rem;" controls preload="metadata">
                                <source :src="`/video-get/${file.filename}`" />
                            </video>
                        </div>
                        <div v-if="file.extension === 'jfif' || file.extension === 'jpg' || file.extension === 'jpeg' || file.extension === 'png'">
                            <img
                                :src="`/image-get/${file.filename}`" alt="Img" class="imageSize rounded-md"
                            />
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
            <span class="mx-2 mt-2 text-xs leading-none text-gray-800">{{ calculateMessageTime(currentMessageSeenBy.created_at) }}</span>
            <div class="mt-6 w-full flex justify-center" v-if="loadingMessageSeenBy">
                <loading-spinner />
            </div>
            <ul v-if="!loadingMessageSeenBy" class="pt-6 mt-3 border-t-2 border-gray-200">
                <li v-for="seenBy in currentMessageSeenBy.seen_by" :key="seenBy.id" class="mb-4">
                    <div class="w-full flex">
                        <div class="mr-4 relative">
                            <img :src="seenBy.user.profile_photo_path ? '/get-profile-photos/'+seenBy.user.profile_photo_path : '/user.png'"
                                 class="w-12 h-12 object-cover bg-gray-700 rounded-full mr-4"
                                 alt="img"
                                 :class="!seenBy.user.profile_photo_path && 'contrast'"
                            >
                            <span v-if="seenBy.user.status === 'online'" class="absolute bottom-0 right-0 w-4 h-4 bg-green-400 rounded-full"></span>
                        </div>

                        <div class="flex w-full items-center justify-between">
                            <div class="flex flex-col justify-center">
                                <h2 class="text-sm font-bold">{{ seenBy.user.name }}</h2>
                                <p class="text-xs font-normal text-gray-600">{{ seenBy.user.email }}</p>
                            </div>
                            <div v-if="seenBy.wseen" class="flex flex-col items-end">
                                <span class="text-xs text-gray-500">
                                    {{ getFormattedDate(seenBy.wseen) }}
                                </span>
                                <span class="text-sm text-gray-600 font-semibold">
                                    {{ getFormattedTime(seenBy.wseen) }}
                                </span>
                            </div>
                            <div v-else class="text-sm text-gray-600 font-semibold">
                                not seen
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</template>

<script>
import moment from "moment";
import LoadingSpinner from "@/Components/LoadingSpinner";

export default {
    name: "MessagesSeenBy",
    components: {
        LoadingSpinner
    },
    props: {
        currentMessageSeenBy: Object,
        changeCurrentLeftSide: Function,
        setCurrentMessageSeenBy: Function,
        loadingMessageSeenBy: Boolean
    },
    data() {
        return {
            loadingMessage: false,
        }
    },
    methods: {
        calculateMessageTime(createdAt) {
            return moment(createdAt).fromNow();
        },
        getFormattedDate(datetime) {
            return moment(datetime).format('DD MMMM YYYY');
        },
        getFormattedTime(datetime){
            return moment(datetime).format('HH:mm')
        },
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

            // Detect video - youtube embedd
            if (inputText.match(/(youtube|youtu)\.(com|be)\/((watch\?v=([-\w]+))|(video\/([-\w]+))|(projects\/([-\w]+)\/([-\w]+))|([-\w]+))/)) {
                replacedText = inputText;
            }

            // Detect video - vimeo embedd
            if (inputText.match(/(vimeo)\.(com|be)\/((watch\?v=([-\w]+))|(video\/([-\w]+))|(projects\/([-\w]+)\/([-\w]+))|([-\w]+))/)) {
                replacedText = `<div id="vimeoWrapper">${inputText}</div>`;
            }

            // Detect video - dailymotion embedd
            if (inputText.match(/(dailymotion)\.(com|be)\/((watch\?v=([-\w]+))|(video\/([-\w]+))|(projects\/([-\w]+)\/([-\w]+))|([-\w]+))/)) {
                replacedText = `<div id="dailymotionWrapper2">${inputText}</div>`;
            }

            return replacedText;

        },
    },
    mounted() {
        if (!this.currentMessageSeenBy) {
            this.changeCurrentLeftSide('');
            this.loadingMessage = false;
        }
        const embedded = document.querySelectorAll('iframe');
        if (embedded && embedded.length > 0) {
            embedded.forEach(embed => {
                embed.width = "100%";
            });
        }
        const dailymotionWrapper = document.getElementById('dailymotionWrapper2');
        if (dailymotionWrapper && dailymotionWrapper.firstChild) {
            dailymotionWrapper.firstChild.style.width = "100%";
            dailymotionWrapper.firstChild.style.height = "20rem !important";
        }
        const vimeoWrapper = document.getElementById('vimeoWrapper');
        if (vimeoWrapper && vimeoWrapper.firstChild) {
            vimeoWrapper.firstChild.style.width = "100%";
            vimeoWrapper.firstChild.style.height = "20rem";
        }
    },
    unmounted() {
        this.setCurrentMessageSeenBy(null);
    }
}
</script>

<style scoped>
    .customScroll::-webkit-scrollbar {
        width: 6px;               /* width of the entire scrollbar */
    }
    .customScroll::-webkit-scrollbar-track {
        background: lightgrey;        /* color of the tracking area */
    }
    .customScroll::-webkit-scrollbar-thumb {
        background-color: gray;    /* color of the scroll thumb */
        border-radius: 10px;       /* roundness of the scroll thumb */ /* creates padding around scroll thumb */
    }
    /* Mozilla Support */
    .customScroll {
        scrollbar-color: gray lightgrey;
        scrollbar-width: thin;
    }
    .contrast {
        filter: grayscale(1) invert(1);
    }
    .imageSize {
        max-width: 100%;
        height: auto;
        max-height: 150px;
        object-fit: contain;
        object-position: center;
    }
</style>
