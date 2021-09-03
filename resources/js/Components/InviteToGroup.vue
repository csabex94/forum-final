<template>
    <div class="px-5 relative">
        <input
            v-model="searchUserQuery"
            class="w-full py-1 pl-10 text-sm text-gray-700 bg-gray-200 border-gray-300 rounded focus:border-gray-500 search-input focus:ring-transparent"
            type="text"
            placeholder="Search by name or email address"
            autocomplete="off"
            autocorrect="off"
        />

        <div class="mt-6 absolute loadingPosition w-full flex justify-center" v-if="loadingResult">
            <loading-spinner />
        </div>

        <ul class="mt-5">
            <li v-for="user in searchResult" :key="user.id" class="flex mt-5 w-full items-center my-3">
                <div class="w-full flex">
                    <div class="mr-2">
                        <img :src="user.profile_photo_path ? '/get-profile-photos/'+user.profile_photo_path : '/user.png'"
                             class="w-12 h-12 bg-gray-700 rounded-full object-cover mr-2"
                             :class="!user.profile_photo_path && 'contrast'"
                             alt="">
                    </div>
                    <div class="flex w-full items-center justify-between">
                        <div class="flex flex-col justify-center">
                            <h2 class="text-sm font-bold">{{ user.name }}</h2>
                            <span class="text-sm text-gray-400">{{ user.email }}</span>
                        </div>

                        <div v-if="user.invited && user.invited === true" class="flex flex-col items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="text-xs mt-1 text-gray-600">Pending</span>
                        </div>

                        <div v-else class="flex items-center">
                            <div v-if="addingTeamMember" class="mr-2 p-1">
                                <loading-spinner />
                            </div>
                            <button
                                @click="addContact(user)"
                                type="button"
                                class="flex flex-col justify-center items-center p-1  w-8 h-8 text-gray-400 rounded-full hover:text-gray-600 hover:bg-gray-300 focus:outline-none focus:ring"
                            >
                                <svg class="fill-current h-6 w-6" viewBox="0 0 24 24">
                                    <path d="M15 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0-6c1.1 0 2 .9 2 2s-.9 2-2 2-2-.9-2-2 .9-2 2-2zm0 8c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4zm-6 4c.22-.72 3.31-2 6-2 2.7 0 5.8 1.29 6 2H9zm-3-3v-3h3v-2H6V7H4v3H1v2h3v3z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </li>
            <div v-if="searchResult.length === 0" class="w-full flex items-center justify-center my-6">
                <img src="/groupinviteill.svg" alt="..." style="opacity: .6; width: 18rem">
            </div>
        </ul>

    </div>
</template>

<script>
    import _ from 'lodash';
    import moment from 'moment';
    import LoadingSpinner from '@/Components/LoadingSpinner';

    export default {
        components: {
            LoadingSpinner
        },
        props: {
            findUsers: Array,
            setNewTeamInvitation: Function,
            changeCurrentComponent: Function,
            setTeamInvitations: Function
        },
        data() {
            return {
                searchUserQuery: "",
                searchResult: [],
                loadingResult: false,
                addingTeamMember: false
            }
        },
        methods: {
            searchUser: _.debounce(function() {
                this.searchResult = [];
                this.loadingResult = true;
                window.axios.get(`/get-user-to-invite?q=${this.searchUserQuery}`).then(res => {
                    this.searchResult = res.data;
                    this.loadingResult = false;
                    console.log(this.searchResult);
                });
            }, 400),
            addTeamMember(user) {
                this.addMemberForm.email = user.email;
                this.addMemberForm.userId = user.id;
                this.addMemberForm.role = 'editor'
                this.addMemberForm.post(route('team-members.store', this.$page.props.user.current_team), {
                    errorBag: 'addTeamMember',
                    preserveScroll: true,
                    onSuccess: () => {
                        this.addMemberForm.reset();
                    }
                });
            },
            addContact(user) {
                this.addingTeamMember = true;
                window.axios.post('/add-group-member', {
                    email: user.email,
                    name: user.name,
                    teamType: "group",
                    role: "admin",
                    userId: user.id,
                    receiverId: user.id,
                    userPhoto: user.profile_photo_path
                }
                ).then(res => {
                    this.searchUserQuery = "";
                    this.searchResult = [];
                    this.setNewTeamInvitation(res.data.groupInvitation);
                    this.addingTeamMember = false;
                });
            },
            getFormattedDate(date) {
                return moment(date).format("DD MMM YYYY H:m")
            }
        },
        watch: {
            searchUserQuery(value) {
                this.searchUser();
            }
        }
    }
</script>

<style scoped>
.contrast {
    filter: grayscale(1) invert(1);
}
.loadingPosition {
    z-index: 60;
}
</style>
