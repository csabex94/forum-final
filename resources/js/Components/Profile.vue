<template>
    <div class="h-full p-2 overflow-y-auto bg-white rounded-r-md customScroll">
        <header class="flex flex-row items-center justify-between">
            <h4 class="text-base font-bold text-gray-500">Profile</h4>
            <button @click="closeSection" type="button" class="p-2 ml-2 text-gray-400 rounded-full hover:text-gray-600 hover:bg-gray-300 focus:outline-none focus:ring">
                <svg class="w-6 h-6 fill-current" viewBox="0 0 20 20">
                    <path d="M15.898,4.045c-0.271-0.272-0.713-0.272-0.986,0l-4.71,4.711L5.493,4.045c-0.272-0.272-0.714-0.272-0.986,0s-0.272,0.714,0,0.986l4.709,4.711l-4.71,4.711c-0.272,0.271-0.272,0.713,0,0.986c0.136,0.136,0.314,0.203,0.492,0.203c0.179,0,0.357-0.067,0.493-0.203l4.711-4.711l4.71,4.711c0.137,0.136,0.314,0.203,0.494,0.203c0.178,0,0.355-0.067,0.492-0.203c0.273-0.273,0.273-0.715,0-0.986l-4.711-4.711l4.711-4.711C16.172,4.759,16.172,4.317,15.898,4.045z"></path>
                </svg>
            </button>
        </header>
        <main>
            <div class="flex flex-col items-center justify-center px-3 mt-4">
                <!-- Change Name -->
                <form @submitted="updateUserPhoto" class="w-full mt-2" enctype="multipart/form-data">
                    <div class="relative flex flex-col items-start justify-between w-full px-2 rounded-md">
                        <div class="flex flex-col items-center justify-between w-full">
                            <div class="flex flex-col" v-show="! photoPreview">
                                <img v-if="$page.props.user.profile_photo_path" :src="'/get-profile-photos/'+$page.props.user.profile_photo_path" class="object-cover w-32 h-32 mb-4 bg-gray-700 rounded-full" alt="">
                                <img v-else src="/user.png" class="w-32 h-32 mb-4 bg-gray-700 rounded-full contrast" alt="">
                            </div>
                            <!-- Profile Photo File Input -->
                            <input type="file" class="hidden"
                                        ref="photo"
                                        @change="updatePhotoPreview"
                            >
                            <div class="flex flex-col items-center justify-between w-full">
                                <button @click="selectNewPhoto"  type="button" class="p-1.5 absolute -top-3 right-10 ml-2 text-gray-400 rounded-full hover:text-gray-600 bg-gray-300 focus:outline-none focus:ring">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                    </svg>
                                </button>

                                <button @click.prevent="deletePhoto" v-if="$page.props.user.profile_photo_path"  type="button" class="p-1.5 absolute top-6 right-10 ml-2 text-gray-400 rounded-full hover:text-gray-600 bg-gray-300 focus:outline-none focus:ring">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>


                                <jet-input-error :message="userInfoform.errors.photo" class="mt-2" />
                            </div>
                        </div>

                    </div>
                </form>

                <form @submit.prevent="updateUserInfo" class="w-full mt-2">
                    <div class="flex flex-col items-start justify-between w-full px-2 py-2 rounded-md">
                        <span class="block mb-1 text-sm font-semibold text-gray-400">Your name</span>

                        <div v-if="editName" class="flex items-center justify-between w-full">
                            <input
                                v-model="userInfoform.name"
                                type="text"
                                class="w-full py-2 pl-2 mr-2 text-sm text-gray-900 placeholder-gray-900 bg-gray-600 border-gray-300 rounded-md outline-none bg-opacity-10 focus:border-gray-500 focus:ring-transparent focus:outline-none"
                            />
                            <button type="submit" class="p-2 ml-2 text-gray-400 rounded-full hover:text-gray-600 hover:bg-gray-300 focus:outline-none focus:ring">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </button>
                            <button type="button" @click="doneEditingName" class="flex flex-col items-center justify-center p-2 ml-2 text-gray-400 rounded-full hover:text-gray-600 focus:ring-2 hover:bg-gray-300 hover:bg-opacity-30 focus:outline-none" aria-label="Add">
                                <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                    <path d="M15.898,4.045c-0.271-0.272-0.713-0.272-0.986,0l-4.71,4.711L5.493,4.045c-0.272-0.272-0.714-0.272-0.986,0s-0.272,0.714,0,0.986l4.709,4.711l-4.71,4.711c-0.272,0.271-0.272,0.713,0,0.986c0.136,0.136,0.314,0.203,0.492,0.203c0.179,0,0.357-0.067,0.493-0.203l4.711-4.711l4.71,4.711c0.137,0.136,0.314,0.203,0.494,0.203c0.178,0,0.355-0.067,0.492-0.203c0.273-0.273,0.273-0.715,0-0.986l-4.711-4.711l4.711-4.711C16.172,4.759,16.172,4.317,15.898,4.045z"></path>
                                </svg>
                            </button>
                        </div>

                        <div v-else class="flex items-center justify-between w-full">
                            <h2 class="text-base text-gray-700">
                                {{ $page.props.user.name }}
                            </h2>
                            <button @click="editingName" type="button" class="p-2 ml-2 text-gray-400 rounded-full hover:text-gray-600 hover:bg-gray-300 focus:outline-none focus:ring">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    <!-- Change Email -->
                    <div class="flex flex-col items-start justify-between w-full px-2 pb-5 mt-1 rounded-md">
                        <span class="block mb-2 text-sm font-semibold text-gray-400">Your email</span>

                        <div v-if="editEmail" class="flex items-center justify-between w-full">
                            <input
                                v-model="userInfoform.email"
                                type="email"
                                class="w-full py-2 pl-2 mr-2 text-sm text-gray-900 placeholder-gray-900 bg-gray-600 border-gray-300 rounded-md outline-none bg-opacity-10 focus:border-gray-500 focus:ring-transparent focus:outline-none"
                            />
                            <button type="submit" class="p-2 ml-2 text-gray-400 rounded-full hover:text-gray-600 hover:bg-gray-300 focus:outline-none focus:ring">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </button>
                            <button type="button" @click="doneEditingEmail" class="flex flex-col items-center justify-center p-2 ml-2 text-gray-400 rounded-full hover:text-gray-600 focus:ring-2 hover:bg-gray-300 hover:bg-opacity-30 focus:outline-none" aria-label="Add">
                                <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                    <path d="M15.898,4.045c-0.271-0.272-0.713-0.272-0.986,0l-4.71,4.711L5.493,4.045c-0.272-0.272-0.714-0.272-0.986,0s-0.272,0.714,0,0.986l4.709,4.711l-4.71,4.711c-0.272,0.271-0.272,0.713,0,0.986c0.136,0.136,0.314,0.203,0.492,0.203c0.179,0,0.357-0.067,0.493-0.203l4.711-4.711l4.71,4.711c0.137,0.136,0.314,0.203,0.494,0.203c0.178,0,0.355-0.067,0.492-0.203c0.273-0.273,0.273-0.715,0-0.986l-4.711-4.711l4.711-4.711C16.172,4.759,16.172,4.317,15.898,4.045z"></path>
                                </svg>
                            </button>
                        </div>

                        <div v-else class="flex items-center justify-between w-full">
                            <h2 class="text-base text-gray-700">
                                {{ $page.props.user.email }}
                            </h2>
                            <button @click="editingEmail" type="button" class="p-2 ml-2 text-gray-400 rounded-full hover:text-gray-600 hover:bg-gray-300 focus:outline-none focus:ring">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </form>

                <button class="w-full py-2 text-sm text-white bg-gray-400 rounded-md focus:outline-none hover:bg-gray-500 hover:shadow-lg" @click="openChangePasswordModal">Change Password</button>

<!--                Change Password-->
                <jet-dialog-modal :show="showChangePasswordModal" @close="closeChangePasswordModal" max-width="xl">
                    <template #title>
                        <h2 class="text-gray-500 font-sm">Change Password</h2>
                    </template>
                    <template #content>
                        <form @submit.prevent="updatePassword" class="w-full px-2">
                            <span class="block  text-sm  text-gray-400">Current password</span>
                            <input
                                ref="current_password"
                                v-model="userPasswordForm.current_password"
                                id="current_password"
                                type="password"
                                class="w-full py-2 pl-2 mr-2 text-sm text-gray-400 placeholder-gray-900 bg-gray-600 border-gray-300 rounded-md outline-none bg-opacity-10 focus:border-gray-500 focus:ring-transparent focus:outline-none"
                            />
                            <span class="block mt-2 text-sm  text-gray-400">New password</span>
                            <input
                                v-model="userPasswordForm.password"
                                id="password"
                                type="password"
                                class="w-full py-2 pl-2 mr-2 text-sm text-gray-400 placeholder-gray-900 bg-gray-600 border-gray-300 rounded-md outline-none bg-opacity-10 focus:border-gray-500 focus:ring-transparent focus:outline-none"
                            />
                            <span class="block mt-2 text-sm  text-gray-400">Confirm Password</span>
                            <input
                                v-model="userPasswordForm.password_confirmation"
                                id="password_confirmation"
                                type="password"
                                class="w-full py-2 pl-2 mr-2 text-sm text-gray-400 placeholder-gray-900 bg-gray-600 border-gray-300 rounded-md outline-none bg-opacity-10 focus:border-gray-500 focus:ring-transparent focus:outline-none"
                            />
                        </form>
                    </template>
                    <template #footer>
                        <button type="button" @click="closeChangePasswordModal" class="px-5 py-2 text-sm text-white bg-gray-400 rounded-md focus:outline-none hover:bg-gray-600 hover:shadow-lg">Cancel</button>
                        <button @click.prevent="updatePassword" type="submit" class="px-5 py-2 text-sm ml-3 text-white bg-blue-500 rounded-md focus:outline-none hover:bg-blue-600 hover:shadow-lg">Save</button>
                    </template>
                </jet-dialog-modal>


                <div class="w-full px-3 py-2">
                    <label class="inline-flex items-center w-full justify-start mt-3">
                        <jet-checkbox :checked="enableBrowserNotification" @update:checked="handleBrowserNotificationChange" />
                        <span class="ml-2 text-sm text-gray-700">Enable Notifications</span>
                    </label>
                    <label class="inline-flex items-center w-full justify-start mt-3">
                        <jet-checkbox :checked="enableSoundNotification" @update:checked="handleSoundNotificationChange" />
                        <span class="ml-2 text-sm text-gray-700">Enable Notification Sound</span>
                    </label>
                </div>

                <div class="w-full flex items-center justify-center my-6">
                    <img src="/profileill.svg" alt="..." style="opacity: .6; width: 18rem">
                </div>

                <form method="POST" @submit.prevent="logout" class="w-full pt-5">
                    <button type="submit" class="flex items-center justify-center w-full py-2 mr-2 text-sm text-white bg-indigo-300 rounded-md focus:outline-none hover:bg-indigo-500 hover:shadow-lg">
                        <span class="font-semibold">Logout</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                    </button>
                </form>
            </div>
        </main>
    </div>
</template>

<script>
import JetLabel from '@/Jetstream/Label';
import JetInput from '@/Jetstream/Input';
import JetInputError from '@/Jetstream/InputError';
import JetSecondaryButton from '@/Jetstream/SecondaryButton';
import JetDialogModal from '@/Jetstream/DialogModal';
import JetCheckbox from '@/Jetstream/Checkbox';
import Button from "@/Jetstream/Button";


export default {
    name: "Profile",
    components: {
        Button,
        JetLabel,
        JetInput,
        JetInputError,
        JetSecondaryButton,
        JetDialogModal,
        JetCheckbox
    },
    props: {
        changeCurrentLeftSide: Function,
        currentLeftSide: String
    },
    data() {
        return {
            editName: false,
            editEmail: false,
            userInfoform: this.$inertia.form({
                _method: 'put',
                name: this.$page.props.user.name,
                email: this.$page.props.user.email,
                photo: null
            }),
            userPasswordForm: this.$inertia.form({
                _method: 'put',
                current_password: "",
                password: "",
                password_confirmation: ""
            }),
            userPhotoForm: this.$inertia.form({
                _method: 'post',
                user: this.$page.props.user,
                photo: null
            }),
            photoPreview: null,
            showChangePasswordModal: false,
            enableBrowserNotification: true,
            enableSoundNotification: true
        }
    },
    methods: {
        openChangePasswordModal() {
            this.showChangePasswordModal = true;
        },
        closeChangePasswordModal() {
            this.showChangePasswordModal = false;
        },
        editingName() {
            this.editName = true;
            this.userInfoform.name = this.$page.props.user.name
        },
        editingEmail() {
            this.editEmail = true;
            this.userInfoform.email = this.$page.props.user.email
        },
        doneEditingName() {
            this.editName = false;
        },
        doneEditingEmail() {
            this.editEmail = false;
        },
        closeSection() {
            this.changeCurrentLeftSide('');
            this.editName = false;
            this.editEmail = false;
        },
        updatePassword() {
            this.userPasswordForm.put(route('user-password.update'), {
                errorBag: 'updatePassword',
                preserveScroll: true,
                onSuccess: () => {
                    this.userPasswordForm.reset('password', 'password_confirmation', 'current_password');
                    this.closeChangePasswordModal();
                },
                onError: () => {
                    if (this.userPasswordForm.errors.password) {
                        this.userPasswordForm.reset('password', 'password_confirmation')
                        this.$refs.password.focus()
                    }

                    if (this.userPasswordForm.errors.current_password) {
                        this.userPasswordForm.reset('current_password')
                        this.$refs.current_password.focus()
                    }
                }
            })
        },
        updateUserInfo() {
            this.userInfoform.put(route('user-profile-information.update'), {
                errorBag: 'updateProfileInformation',
                preserveScroll: true,
                onSuccess: () => {
                    this.userInfoform.reset();
                    this.editName = false;
                    this.editEmail = false;
                    this.clearPhotoFileInput();
                },
            });
        },
        logout() {
            this.$inertia.post(route('custom.logout'));
        },
        selectNewPhoto() {
            this.$refs.photo.click();
        },
        updatePhotoPreview() {
            const photo = this.$refs.photo.files[0];

            if (! photo) return;

            this.userPhotoForm.photo = photo;
            this.userPhotoForm.post(route('user-profile-information.update-photo'), {
                preserveScroll: true,
                onSuccess: () => {
                    this.userPhotoForm.reset();
                    this.clearPhotoFileInput();
                },
                onError: () => {
                    console.log(this.$page.props.errors);
                }
            });
        },
        deletePhoto() {
            this.$inertia.delete(route('current-user-photo.destroy'), {
                preserveScroll: true,
                onSuccess: () => {
                    this.photoPreview = null;
                    this.clearPhotoFileInput();
                },
            });
        },
        clearPhotoFileInput() {
            if (this.$refs.photo?.value) {
                this.$refs.photo.value = null;
            }
        },
        handleBrowserNotificationChange(data) {
            this.enableBrowserNotification = data.value;
            if (data.value=== true) {
                localStorage.setItem('ebn', 'enabled');
            } else {
                localStorage.setItem('ebn', 'disabled');
            }
        },
        handleSoundNotificationChange(data) {
            this.enableSoundNotification = data.value;
            if (data.value === true) {
                localStorage.setItem('esn', 'enabled');
            } else {
                localStorage.setItem('esn', 'disabled');
            }
        },
        checkNotificationOptions() {
            const browserNotificationOption = localStorage.getItem('ebn');
            const soundNotificationOption = localStorage.getItem('esn');
            if (browserNotificationOption === 'enabled') this.enableBrowserNotification = true;
            if (soundNotificationOption === 'enabled') this.enableSoundNotification = true;
            if (browserNotificationOption === 'disabled') this.enableBrowserNotification = false;
            if (soundNotificationOption === 'disabled') this.enableSoundNotification = false;
        }
    },
    computed: {
        currentLeftSideState() {
            return this.currentLeftSide;
        },
    },
    watch: {
        currentLeftSideState(value) {
            this.editName = false;
            this.editEmail = false;
        }
    },
    mounted() {
        this.checkNotificationOptions();
    }
}
</script>

<style scoped>
    .contrast {
        filter: grayscale(1) invert(1);
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
    /* Mozilla Support */
    .customScroll {
        scrollbar-color: gray lightgrey;
        scrollbar-width: thin;
    }
</style>
