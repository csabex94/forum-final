<template>
    <div style="z-index: 100" class="w-full fixed flex items-center justify-center top-0 left-0 right-0 bottom-0 h-screen bg-gray-700 bg-opacity-90">
        <div class="p-12 flex items-center justify-center w-full h-full">
            <white-loading-spinner v-if="imageLoading" />
            <img
                :src="`/image-get/${imageToOpen.filename}`"
                alt="Image"
                v-if="!imageLoading && imageToOpen.extension === 'jfif' || imageToOpen.extension === 'jpg' || imageToOpen.extension === 'jpeg' || imageToOpen.extension === 'png'"
                class="w-full h-full rounded-md openedImg"
            >
        </div>

        <button @click="closingImage" type="button" class="p-2 absolute top-5 right-5 text-gray-400 rounded-full hover:text-gray-600 hover:bg-gray-300 focus:outline-none focus:ring">
            <svg class="w-6 h-6 fill-current" viewBox="0 0 20 20">
                <path d="M15.898,4.045c-0.271-0.272-0.713-0.272-0.986,0l-4.71,4.711L5.493,4.045c-0.272-0.272-0.714-0.272-0.986,0s-0.272,0.714,0,0.986l4.709,4.711l-4.71,4.711c-0.272,0.271-0.272,0.713,0,0.986c0.136,0.136,0.314,0.203,0.492,0.203c0.179,0,0.357-0.067,0.493-0.203l4.711-4.711l4.71,4.711c0.137,0.136,0.314,0.203,0.494,0.203c0.178,0,0.355-0.067,0.492-0.203c0.273-0.273,0.273-0.715,0-0.986l-4.711-4.711l4.711-4.711C16.172,4.759,16.172,4.317,15.898,4.045z"></path>
            </svg>
        </button>
    </div>
</template>

<script>
import WhiteLoadingSpinner from "@/Components/WhiteLoadingSpinner";

export default {
    name: "ImageFullscreen",
    components: {
        WhiteLoadingSpinner
    },
    props: {
        imageToOpen: Object,
        closingImage: Function
    },
    data() {
        return {
            imageLoading: true
        }
    },
    methods: {
        loadImage(url) {
            return new Promise((resolve, reject) => {
                const img = new Image();
                img.addEventListener('load', () => resolve(img));
                img.addEventListener('error', (err) => reject(err));
                img.src = url;
            });
        }
    },
    created() {
        this.loadImage('/image-get/'+this.imageToOpen.filename).then(() => {
            this.imageLoading = false;
        }).catch(error => {
            console.log({customError: error})
        })
    }
}
</script>

<style scoped>
    .openedImg {
        height: auto;
        max-height: 100%;
        width: auto;
        object-position: center;
        object-fit: cover;
        z-index: 100;
    }
</style>
