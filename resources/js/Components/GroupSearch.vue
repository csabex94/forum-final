<template>
        <div class="w-full h-screen flex overflow-x-hidden flex-col overflow-y-auto justify-start customScroll bg-white">
            <header class="flex flex-row items-start justify-between">
                <div class="flex flex-row items-start justify-between w-full p-2">
                    <div class="flex w-full flex-col">
                        <h4 class="text-base my-2 font-bold text-gray-500">Search</h4>
                    </div>

                    <button @click="changeCurrentLeftSide('')" type="button" class="p-2 ml-2 text-gray-400 rounded-full hover:text-gray-600 hover:bg-gray-300 focus:outline-none focus:ring">
                        <svg class="w-6 h-6 fill-current" viewBox="0 0 20 20">
                            <path d="M15.898,4.045c-0.271-0.272-0.713-0.272-0.986,0l-4.71,4.711L5.493,4.045c-0.272-0.272-0.714-0.272-0.986,0s-0.272,0.714,0,0.986l4.709,4.711l-4.71,4.711c-0.272,0.271-0.272,0.713,0,0.986c0.136,0.136,0.314,0.203,0.492,0.203c0.179,0,0.357-0.067,0.493-0.203l4.711-4.711l4.71,4.711c0.137,0.136,0.314,0.203,0.494,0.203c0.178,0,0.355-0.067,0.492-0.203c0.273-0.273,0.273-0.715,0-0.986l-4.711-4.711l4.711-4.711C16.172,4.759,16.172,4.317,15.898,4.045z"></path>
                        </svg>
                    </button>
                </div>
            </header>
            <div class="w-full flex flex-col items-stretch justify-start p-3 bg-white">
                <div class="flex flex-col flex-auto">
                    <label id="listbox-label" class="block text-sm font-medium text-gray-700">
                        Search for:
                    </label>
                    <div class="mt-1 relative">
                        <select
                            name="type"
                            @change="changeSearch($event)"
                            class="w-full py-2 text-sm text-gray-700 bg-gray-200 border-gray-300 rounded focus:border-gray-500 focus:ring-transparent"
                            v-model="type"
                        >
                            <option value="1" class="py-1" selected>Files</option>
                            <option value="2" class="py-1">Media</option>
                            <option value="3" class="py-1">Messages</option>
                            <option v-if="$page.props.user.current_team.team_type === 'group'" value="4" class="py-1">Topics</option>
                        </select>
                    </div>

                    <input
                        v-model="searchQuery"
                        class="w-full py-2 mt-3 text-sm search-input pl-10 text-gray-700 bg-gray-200 border-gray-300 rounded focus:border-gray-500 focus:ring-transparent"
                        type="text" placeholder="Search"
                    />

                    <div
                        v-if="messageResult.length === 0 && fileResult.length === 0 && mediaResult.length === 0 && topicResult.length === 0 && !loadingResults"
                        class="w-full mt-12 flex items-center justify-center"
                    >
                        <img src="/groupsearchill.svg" alt="..." class="illSize" style="opacity: 0.6; width: 22rem">
                    </div>

                    <div v-if="loadingResults" class="w-full flex items-center justify-center mt-16">
                        <loading-spinner />
                    </div>

                </div>
            </div>

           <div class="px-3">
               <ul v-if="parseInt(type) === 1 && fileResult.length > 0" class="grid grid-cols-3 gap-2 my-3">
                   <span v-if="fileResult.length === 0" class="w-full my-6 text-gray-600 font-semibold">No result.</span>

                   <li v-for="file in fileResult" :key="file.id" class="">
                       <a
                           :href="'/download/' + file.filename" target="_blank"
                           class="flex flex-col items-center justify-center mt-3 text-xs font-semibold"
                           v-if="file.team_id === $page.props.user.current_team_id"
                       >
                           <div class="p-2 rounded-full hover:bg-gray-200 bg-gray-100 shadow-md flex items-center justify-center">
                               <img v-if="file.extension === 'pdf'" src="/pdf.png" alt="pdf" class="w-8 h-8 m-2" />
                               <img v-if="file.extension === 'xlsx'" src="/excel.png" alt="Excell" class="w-8 h-8 m-2" />
                               <img v-if="file.extension === 'doc'" src="/word.png" alt="Word" class="w-8 h-8 m-2" />
                               <img v-if="file.extension === 'rtf' || file.extension === 'txt'" src="/doc.png" alt="Document" class="w-8 h-8 m-2" />
                           </div>
                           <span class="mt-1">{{ reduceFileName(file.original_filename) }}</span>
                       </a>
                   </li>
               </ul>
               <ul v-if="parseInt(type) === 2 && mediaResult.length > 0" class="grid grid-cols-3 gap-2 my-3">
                   <span v-if="mediaResult.length === 0" class="w-full my-6 text-gray-600 font-semibold">No result.</span>
                   <li v-for="file in mediaResult" :key="file.id">
                       <img
                           @click="openningImage(file)"
                           :ref="`convImg-${file.id}`"
                           :src="`/image-get/${file.filename}`" alt="Img" class="rounded-md cursor-pointer imageSize"
                       />
                   </li>
               </ul>
               <ul v-if="parseInt(type) === 3 && messageResult.length > 0" class="grid grid-cols-1 my-3">
                   <span v-if="messageResult.length === 0" class="my-6 text-gray-600 font-semibold">No result.</span>
                   <li v-for="message in messageResult" :key="message.id" class="w-full mt-1">
                       <div class="flex items-start w-full">
                           <div class="bg-blue-400 text-white rounded-l-lg shadow rounded-br-lg w-full p-3 mt-3">
                                <span class="mt-1">{{ message.created_by.name }}:</span>
                                <p v-html="linkify(message.message)" class="text-sm flex flex-col max-w-2xl text-white"></p>
                               <span class="text-xs mt-2 ml-1">{{ calculateMessageTime(message.created_at) }}</span>
                           </div>
                           <iv>
                               <img
                                   class="w-8 h-8 ml-2 rounded-full object-cover"
                                   :src="getUserPhoto(message.created_by.profile_photo_path)"
                                   :class="!message.created_by.profile_photo_path && 'contrast'"
                                   alt="..."
                               >
                           </iv>
                       </div>
                   </li>
               </ul>
               <ul v-if="parseInt(type) === 4 && topicResult.length > 0">
                   <li v-for="topic in topicResult" :key="topic.id" class="rounded shadow border border-gray-200 mb-3 p-2">
                       <div class="text-xs text-gray-600">{{ calculateMessageTime(topic.created_at) }}</div>
                       <div class="text-base font-bold text-gray-600">{{ topic.title }}</div>
                       <div>{{ topic.description }}</div>

                       <div class="flex items-center justify-between mt-4">
                           <inertia-link
                               @click="setLoadingMiddleSection(true)"
                               :href="route('home', { topicId: topic.id, tn: topic.title })"
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
                   </li>
               </ul>
           </div>
        </div>

</template>

<script>

import LoadingSpinner from '@/Components/LoadingSpinner';
import moment from 'moment';

export default {
    name: "GroupSearch",
    components: {
        LoadingSpinner,
    },
    props: {
        changeCurrentLeftSide: Function,
        setSearchOpenedImage: Function,
        setLoadingMiddleSection: Function
    },
    data() {
        return {
            SVGPathSegCurvetoQuadraticSmoothRel: "",
            type: 1,
            searchQuery: "",
            results: [],
            openImage: false,
            imageToOpen: null,
            loadingResults: false,
            searchResult: [],
            fileResult: [],
            mediaResult: [],
            messageResult: [],
            topicResult: []
        }
    },
    methods: {
        getUserPhoto(photo) {
            if (photo) {
                return `/get-profile-photos/${photo}`;
            } else {
                return '/user.png'
            }
        },
        findMessages: _.debounce(function() {
            this.loadingResults = true;
            if(this.searchQuery === '') {
                this.fileResult = [];
                this.mediaResult = [];
                this.messageResult = [];
                this.topicResult = [];
                this.loadingResults = false;
            } else {
                window.axios.get(`/search-messages?search=${this.searchQuery}&type=${this.type}`).then(res => {
                    if (parseInt(res.data.type) === 1) {
                        this.fileResult = res.data.result;
                    }
                    if (parseInt(res.data.type) === 2) {
                        this.mediaResult = res.data.result;
                    }
                    if (parseInt(res.data.type) === 3) {
                        this.messageResult = res.data.result;
                    }
                    if (parseInt(res.data.type) === 4) {
                        this.topicResult = res.data.result;
                    }
                    this.loadingResults = false;
                })
            }
        }, 400),
        changeSearch(event) {
            if (this.searchQuery && this.searchQuery.length > 0) {
                this.findMessages();
            }
        },
        calculateMessageTime(createdAt) {
            return moment(createdAt).format('DD MMM YYYY HH:MM');
        },
        reduceFileName(filename) {
            if (filename.length > 15) {
                return filename.substring(0, 15)+"...";
            } else {
                return filename;
            }
        },
        openningImage(img) {
            this.openImage = true;
            this.imageToOpen = img;
            this.setSearchOpenedImage(img);
        },
        closingImage() {
            this.openImage = false;
            this.imageToOpen = null;
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

            // Detect video
            if (inputText.match(/(youtube|youtu|vimeo|dailymotion|kickstarter)\.(com|be)\/((watch\?v=([-\w]+))|(video\/([-\w]+))|(projects\/([-\w]+)\/([-\w]+))|([-\w]+))/)) {
                replacedText = `<div id="embeddedVideo">${inputText}</div>`;
            }

            return replacedText;

        },
    },
    watch: {
        searchQuery(newVal, oldVal) {
            this.findMessages();
        },
    },
    mounted() {
        const embedded = document.getElementById('embeddedVideo');
        if (embedded) {
            if (embedded.firstChild && embedded.firstChild.tagName === 'IFRAME') {
                embedded.firstChild.width = 400;
            }
        }
    }
}
</script>

<style scoped>
    .imageSize {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center;
    }
    .openedImg {
        height: 100%;
        width: 100%;
        object-position: center;
        object-fit: contain;
    }
</style>
