<template>
    <div class="w-full">
        <div class="w-full pb-3 flex flex-col items-start justify-between">
            <div class="w-full px-2 grid grid-cols-7 gap-6 py-4 text-xs font-bold text-gray-600">
                <div @click="setCurrentOption('trending')" :class="currentOption === 'trending' && 'bg-gray-200'" class="w-full cursor-pointer h-full rounded hover:bg-gray-200 py-2 flex items-center justify-center">
                    <p>Trending</p>
                </div>
                <div @click="setCurrentOption('HAHA')" :class="currentOption === 'HAHA' && 'bg-gray-200'" class="w-full cursor-pointer h-full flex items-center justify-center rounded hover:bg-gray-200">
                    <p>Funny</p>
                </div>
                <div @click="setCurrentOption('SAD')" :class="currentOption === 'SAD' && 'bg-gray-200'" class="w-full cursor-pointer h-full flex items-center justify-center rounded hover:bg-gray-200">
                    <p>Sad</p>
                </div>
                <div @click="setCurrentOption('LOVE')" :class="currentOption === 'LOVE' && 'bg-gray-200'" class="w-full cursor-pointer h-full flex items-center justify-center rounded hover:bg-gray-200">
                    <p>Love</p>
                </div>
                <div @click="setCurrentOption('SPORT')" :class="currentOption === 'SPORT' && 'bg-gray-200'" class="w-full cursor-pointer h-full flex items-center justify-center rounded hover:bg-gray-200">
                    <p>Sport</p>
                </div>
                <div @click="setCurrentOption('REACTIONS')" :class="currentOption === 'REACTIONS' && 'bg-gray-200'" class="w-full cursor-pointer h-full flex items-center justify-center rounded hover:bg-gray-200">
                    <p>Reactions</p>
                </div>
                <div class="w-full h-full flex items-center pb-3 justify-end">
                    <button v-if="showGifSearch" @click="closeGifSearch" type="button" class="w-8 h-8 flex items-center justify-center h-12 text-gray-400 rounded-full hover:text-gray-600 hover:bg-gray-200 focus:outline-none" aria-label="Upload a files">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </div>
            <div class="flex w-full justify-between items-center">
                <input
                    v-model="gifSearchField"
                    type="text"
                    placeholder="Search GIF (Powered by GIPHY)"
                    class="w-full py-2 mr-auto text-sm text-gray-700 placeholder-gray-500 bg-gray-600 border-gray-300 rounded-md outline-none bg-opacity-10 focus:border-gray-500 focus:ring-transparent focus:outline-none"
                />
            </div>
        </div>
        <div id="gifScrollArea" class="w-full gifSearchSize overflow-y-auto customScroll">
            <div v-if="loadingGifs" class="w-full h-full flex items-center justify-center">
                <loading-spinner />
            </div>

            <div v-if="gifs.length > 0 && !loadingGifs" class="w-full h-full mt-8 flex flex-wrap">
                <img @click="handleGifClick(gif)" v-for="(gif, i) in gifs" :key="i" :src="gif" class="w-40 h-32 m-1 cursor-pointer" alt="...">
            </div>
        </div>
    </div>
</template>

<script>
import LoadingSpinner from "@/Components/LoadingSpinner";

export default {
    name: "GifSearch",
    components: {
        LoadingSpinner
    },
    props: {
        showGifSearch: Boolean,
        closeGifSearch: Function,
        setMessagePrivate: Function,
        writeMessagePrivate: Function
    },
    data() {
        return {
            gifSearchField: "",
            gifs: [],
            gifsPage: 0,
            currentOption: 'trending',
            loadingGifs: false,
            gifCount: 15,
            gifsScrollTop: 0,
            isScrolling: false
        }
    },
    methods: {
        setScrollPosition() {
            let timeout;
            const gifs = document.getElementById('gifScrollArea');
            clearTimeout(timeout);
            timeout = setTimeout(() => {
                if (!this.isScrolling) {
                    gifs.scrollTop = this.gifsScrollTop;
                }
            }, 100);
        },
        setCurrentOption(option) {
            this.gifCount = 15;
            this.gifsPage = 0;
            this.gifsScrollTop = 0;
            this.currentOption = option;
            this.getInitGifs();
        },
        buildGifs(json) {
            this.gifs = json.data.map(gif => gif.id).map(gifId => {
                return `https://media.giphy.com/media/${gifId}/giphy.gif`;
            });
            this.setScrollPosition();
        },
        getInitGifs() {
            this.loadingGifs = true;
            let apiKey = "J5fh8DT2oaHwTIUtGK57oDsE47j18XMM";
            let searchEndPoint = "https://api.giphy.com/v1/gifs/search?";

            if (this.currentOption === 'trending') {
                searchEndPoint = "https://api.giphy.com/v1/gifs/trending?";
            } else {
                searchEndPoint = `https://api.giphy.com/v1/gifs/search?q=${this.currentOption}`;
            }

            let url = `${searchEndPoint}&api_key=${apiKey}&limit=${this.gifCount}&offset=${this.gifsPage}`;
            fetch(url)
                .then(response => {
                    return response.json();
                })
                .then(json => {
                    this.buildGifs(json);
                    this.loadingGifs = false;
                })
                .catch(err => {
                    console.log(err);
                });
        },
        handleGifSearch: window._.debounce(function() {
            this.loadingGifs = true;
            let apiKey = "J5fh8DT2oaHwTIUtGK57oDsE47j18XMM";
            let searchEndPoint = "https://api.giphy.com/v1/gifs/search?";
            let url = `${searchEndPoint}&api_key=${apiKey}&q=${this.gifSearchField}&limit=${this.gifCount}&offset=${this.gifsPage}`;
            fetch(url)
                .then(response => {
                    return response.json();
                })
                .then(json => {
                    this.buildGifs(json);
                    this.loadingGifs = false;
                })
                .catch(err => {
                    console.log(err);
                });
        }, 400),
        handleGifClick(gif) {
            this.setMessagePrivate(gif);
            this.gifs = [];
            this.gifSearchField = "";
            this.closeGifSearch();
            this.writeMessagePrivate();
        },
        detectScrollToBottom() {
            const gifsArea = document.getElementById('gifScrollArea');
            if (gifsArea) {
                this.isScrolling = false;
                gifsArea.addEventListener('scroll', async () => {
                    if( gifsArea.clientHeight === (gifsArea.scrollHeight - gifsArea.scrollTop)) {
                        if (this.gifs.length <= 49 && !this.isScrolling) {
                            this.isScrolling = true;
                            this.gifsPage++;
                            this.gifCount = this.gifCount + 15;
                            this.gifsScrollTop = this.gifsScrollTop + 200;
                            if (this.gifSearchField || this.gifSearchField.trim() !== "") {
                                await this.handleGifSearch();
                            } else {
                                await this.getInitGifs();
                            }
                            this.isScrolling = false;
                        }
                    }
                });
            }
        }
    },
    watch: {
        gifSearchField(newValue, oldVaalue) {
            if (newValue.length > 0){
                this.gifCount = 15;
                this.gifsPage = 0;
                this.gifsScrollTop = 0;
                this.handleGifSearch();
            }
        },
    },
    mounted() {
        this.getInitGifs();
        this.detectScrollToBottom();
    }
}
</script>

<style scoped>
.gifSearchSize {
    height: 20rem;
}
.resultSize {
    height: 15rem;
}
</style>
