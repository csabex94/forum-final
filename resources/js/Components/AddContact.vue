<template>
    <div :class="currentRightSide === 'add-contact' ? 'slideIn' : 'slideOut'" class="absolute w-full flex flex-col items-stretch justify-start rounded-md addContact bg-gray-50 lg:rounded-none lg:rounded-l-md">
        <div class="flex flex-col flex-auto">
            <div class="flex flex-row flex-auto">
                <div class="w-full p-3">
                    <form autoComplete="off">
                        <div class="flex items-start w-full p-1">
                            <input autocorrect="off" autocomplete="off" id="contactName" v-model="fc" class="w-full py-1 text-sm text-gray-700 bg-gray-200 border-gray-300 rounded focus:border-gray-500 focus:ring-transparent" type="text" placeholder="Contact name or email address" />
                            <button type="button" @click="changeRightSide('groups')" class="flex flex-col items-center justify-center p-2 ml-2 rounded-full focus:ring-2 hover:bg-gray-200 hover:bg-opacity-30 focus:outline-none" aria-label="Add">
                                <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                    <path d="M15.898,4.045c-0.271-0.272-0.713-0.272-0.986,0l-4.71,4.711L5.493,4.045c-0.272-0.272-0.714-0.272-0.986,0s-0.272,0.714,0,0.986l4.709,4.711l-4.71,4.711c-0.272,0.271-0.272,0.713,0,0.986c0.136,0.136,0.314,0.203,0.492,0.203c0.179,0,0.357-0.067,0.493-0.203l4.711-4.711l4.71,4.711c0.137,0.136,0.314,0.203,0.494,0.203c0.178,0,0.355-0.067,0.492-0.203c0.273-0.273,0.273-0.715,0-0.986l-4.711-4.711l4.711-4.711C16.172,4.759,16.172,4.317,15.898,4.045z"></path>
                                </svg>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="mt-6 w-full flex justify-center" v-if="loadingResult">
                <loading-spinner />
            </div>
            <ul v-if="findContactsResult" class="contactList overflow-y-auto customScroll w-full h-full px-3">
                <div v-if="findContactsResult.length === 0 && !loadingResult" class="mt-16 pl-8">
                    <img src="/addcontactill.svg" alt="..." style="opacity: 0.6; width: 22rem;">
                </div>

                <li v-for="contact in findContactsResult" :key="contact.id" class="flex flex-row my-3">
                    <div class="mr-2">
                        <img :src="contact.profile_photo_path ? '/get-profile-photos/'+contact.profile_photo_path : '/user.png'"
                             class="w-12 h-12 bg-gray-700 object-cover mr-2 rounded-full"
                             :class="!contact.profile_photo_path && 'contrast'"
                             alt="">
                    </div>
                    <div class="flex w-full items-center justify-between">
                        <div class="flex flex-col justify-center">
                            <h2 class="text-sm font-bold">{{ contact.name }}</h2>
                            <span class="text-sm text-gray-400">{{ contact.email }}</span>
                        </div>

                        <loading-spinner v-if="loadingSendInvite && contact.id === sendingInviteTo" />

                        <div v-if="contact.invited && contact.invited === true" class="flex flex-col items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="text-xs mt-1 text-gray-600">Pending</span>
                        </div>

                        <button
                            v-else-if="contact.team_exists && contact.team_exists === true"
                            type="button"
                            class="flex has-tooltip flex-col justify-center ml-2 items-center p-1  w-8 h-8 text-gray-400 rounded-full hover:text-gray-600 hover:bg-gray-300 focus:outline-none focus:ring"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class='tooltip tooltipPosition w-48 z-50 text-xs rounded-lg shadow-lg  p-2 text-white bg-gray-500'>
                                You already have an invitation from this user. Please check your invitations.
                                </span>
                        </button>


                        <button
                            v-else
                            @click="addContact(contact)"
                            type="button"
                            class="flex flex-col justify-center ml-2 items-center p-1  w-8 h-8 text-gray-400 rounded-full hover:text-gray-600 hover:bg-gray-300 focus:outline-none focus:ring"
                        >
                            <svg class="fill-current h-6 w-6" viewBox="0 0 24 24">
                                <path d="M15 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0-6c1.1 0 2 .9 2 2s-.9 2-2 2-2-.9-2-2 .9-2 2-2zm0 8c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4zm-6 4c.22-.72 3.31-2 6-2 2.7 0 5.8 1.29 6 2H9zm-3-3v-3h3v-2H6V7H4v3H1v2h3v3z"/>
                            </svg>
                        </button>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</template>

<script>
import _ from 'lodash';
import LoadingSpinner from "@/Components/LoadingSpinner";

export default {
    name: "AddContact",
    components: {
        LoadingSpinner
    },
    props: {
        changeCurrentRightSide: Function,
        currentRightSide: String,
        findContacts: Array,
        setNewGroups: Function,
        setNewGroupsCopy: Function,
        setNewPendingInvitation: Function,
    },
    data() {
        return {
            fc: "",
            findContactsResult: [],
            loadingResult: false,
            loadingSendInvite: false,
            sendingInviteTo: null,
            addMemberForm: this.$inertia.form({
                _method: 'post',
                name: '',
                teamType: 'private-message',
                receiverId: null
            }),
            addTeamMemberForm: this.$inertia.form({
                email: '',
                role: 'admin',
                userId: null
            }),
        }
    },
    methods: {
        findContact: _.debounce(function() {
            this.findContactsResult = [];
            this.loadingResult = true;
            window.axios.get(`/find-contacts?q=${this.fc}`).then(res => {
                this.findContactsResult = res.data;
                this.loadingResult = false;
            })
        }, 400),
        changeRightSide(component) {
            this.changeCurrentRightSide(component);
            this.fc = "";
        },
        addContact(user) {
            this.sendingInviteTo = user.id;
            this.loadingSendInvite = true;
            window.axios.post('/add-contact',
                {
                    email: user.email,
                    name: user.name,
                    teamType: "private-message",
                    role: "admin",
                    userId: user.id,
                    receiverId: user.id,
                    userPhoto: user.profile_photo_path
                }
            ).then(res => {
                if (res.data) {
                    this.setNewGroupsCopy(res.data.teams);
                    this.changeCurrentRightSide('pending');
                    this.setNewPendingInvitation(res.data.newInvitation);
                }
                this.addMemberForm.name = "";
                this.addMemberForm.receiverId = "";
                this.fc = "";
                this.addTeamMemberForm.reset();
                this.loadingSendInvite = false;
                this.sendingInviteTo = null;
            });
        }
    },
    watch: {
        fc: function(value) {
            this.findContact()
        }
    },
}
</script>

<style scoped>
    .addContact {
        top: 0;
        left: 0;
        height: 100%;
        transition: transform .7s;
    }
    .slideOut {
        transform: translateX(-100%);
    }
    .slideIn {
        transform: translateX(0);
    }
    .contrast {
        filter: grayscale(1) invert(1);
    }
    .contactList {
        height: 95vh;
    }
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
    .customScroll {
        scrollbar-color: gray lightgrey;
        scrollbar-width: thin;
    }
    .tooltipPosition {
        z-index: 65;
        top: 2rem;
        right: 0;
    }
</style>
