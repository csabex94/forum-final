<template>
    <div class="h-screen p-2 bg-white rounded-r-md overflow-y-auto customScroll">
        <header class="flex flex-row items-center justify-between">
            <h4 class="text-base font-bold text-gray-500">Notifications ({{ mappedTotalNotifications.length }})</h4>
            <button @click="changeCurrentLeftSide('')" type="button" class="p-2 ml-2 text-gray-400 rounded-full hover:text-gray-600 hover:bg-gray-300 focus:outline-none focus:ring">
                <svg class="w-6 h-6 fill-current" viewBox="0 0 20 20">
                    <path d="M15.898,4.045c-0.271-0.272-0.713-0.272-0.986,0l-4.71,4.711L5.493,4.045c-0.272-0.272-0.714-0.272-0.986,0s-0.272,0.714,0,0.986l4.709,4.711l-4.71,4.711c-0.272,0.271-0.272,0.713,0,0.986c0.136,0.136,0.314,0.203,0.492,0.203c0.179,0,0.357-0.067,0.493-0.203l4.711-4.711l4.71,4.711c0.137,0.136,0.314,0.203,0.494,0.203c0.178,0,0.355-0.067,0.492-0.203c0.273-0.273,0.273-0.715,0-0.986l-4.711-4.711l4.711-4.711C16.172,4.759,16.172,4.317,15.898,4.045z"></path>
                </svg>
            </button>
        </header>
        <div v-if="currentNotifications === 'all'" class="mt-5">
            <div v-if="loadingNotifications" class="w-full h-full flex items-center justify-center">
                <loading-spinner />
            </div>
            <div v-for="(notification, i) in mappedTotalNotifications" :key="i">
               <div class="flex w-full mt-6 bg-white shadow rounded-lg">
                   <div class="w-2 bg-blue-400 rounded-l-lg"></div>
                   <div class="flex items-start justify-between w-full px-2 py-3">
                       <img
                           alt="user img"
                           class="w-12 h-12 object-cover rounded-full"
                           :src="getUserPhoto(notification.data.topic.created_by)"
                           :class="!notification.data.topic.created_by.profile_photo_path && 'contrast bg-gray-800'"
                       >
                       <div class="mx-3 flex-grow">
                           <h2 class="text-base font-semibold text-gray-800">New Topic in <span class="text-blue-500 font-bold">{{ notification.data.team.name }}</span></h2>
                           <p class="text-gray-600">
                               <span class="text-blue-400">{{ notification.data.topic.created_by.name }}</span>
                               posted a new topic.</p>
                           <div class="flex w-full justify-between mt-3">
                               <span v-if="!notification.read_at" class="rounded-lg text-white bg-blue-400 px-1 smallText">New</span>
                               <span v-else class="text-gray-500 font-bold px-1 smallText">{{ getNotificationDate(notification.created_at) }}</span>
                           </div>
                       </div>
                   </div>
               </div>
            </div>
        </div>
    </div>
</template>

<script>
import moment from 'moment';
import LoadingSpinner from "@/Components/LoadingSpinner";

export default {
    name: "Notifications",
    components: {LoadingSpinner},
    props: {
        changeCurrentLeftSide: Function,
        unsetUnreadNotificationsCount: Function,
        clearNotifications: Function
    },
    data() {
        return {
            mappedTotalNotifications: [],
            loadingNotifications: false,
            currentNotifications: 'all'
        }
    },
    methods: {
      getUserPhoto(user) {
          if (user.profile_photo_path) {
              return "/get-profile-photos/"+user.profile_photo_path;
          } else {
              return '/user.png'
          }
      },
      getNotificationDate(date){
          return moment(date).format('DD MMM YYYY HH:mm')
      },
    },
    mounted() {
        this.clearNotifications();
        this.loadingNotifications = true;
        window.axios.get('/get-user-notifications').then(res => {
            this.mappedTotalNotifications = res.data;
            this.loadingNotifications = false;
        }).then(() => {
            if (this.mappedTotalNotifications.length > 0) {
                window.axios.get('/read-all-notifications').then(res => res.data);
                this.unsetUnreadNotificationsCount();
            }
        });
    },
}
</script>

<style scoped>

</style>
