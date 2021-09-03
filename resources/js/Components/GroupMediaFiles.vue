<template>
    <ul class="grid grid-cols-3 gap-2 my-3">
        <li class="w-full h-full " v-for="file in mappedMediaFiles" :key="file.id">
            <img
                v-if="!loadingMediaImg && (file.extension === 'jfif' || file.extension === 'jpg' || file.extension === 'jpeg' || file.extension === 'png')
                                && file.team_id === $page.props.user.current_team_id"
                class="object-cover w-40 h-32 cursor-pointer rounded-md"
                alt=" "
                :src="`/image-get/${file.filename}`"
                @click="handleImageClick(file)"
            >
            <div v-if="loadingMediaImg" class="w-full h-full flex items-center justify-center">
                <loading-spinner />
            </div>
        </li>
    </ul>
</template>

<script>
import LoadingSpinner from "@/Components/LoadingSpinner";

export default {
    name: "GroupMediaFiles",
    props: {
        mappedMediaFiles: Array,
        currentGroupStorage: String,
        currentComponent: String
    },
    components: {
        LoadingSpinner
    },
    data() {
        return {
            loadingMediaImg: false
        }
    },
    methods: {
        handleImageClick(url) {
            this.$emit('groupMediaClicked', url);
        }
    },
    // created() {
    //     this.loadingMediaImg = true;
    //     if (this.mappedMediaFiles && this.mappedMediaFiles.length > 0) {
    //         const images = this.mappedMediaFiles.map(file => {
    //             return new Promise((resolve, reject) => {
    //                 const img = new Image();
    //                 img.src = '/image-get/'+file.filename;
    //                 img.onload = resolve;
    //                 img.onerror = reject;
    //             });
    //         });
    //         Promise.all(images).then(() => {
    //             this.loadingMediaImg = false;
    //         }).catch(error => {
    //             console.log({customError: error})
    //         })
    //     }
    // }
}
</script>

<style scoped>

</style>
