<template>
    <div id="main-container" class="h-screen">
        <section class="flex w-full overflow-hidden shadow-xl lg:mx-auto">
            <!-- Left section -->

            <div class="relative w-2/6 ">
                <left-section
                    :newGroups="newGroups"
                    :gotNewMessage="gotNewMessage"
                    :changeCurrentRightSide="changeCurrentRightSide"
                    :changeCurrentLeftSide="changeCurrentLeftSide"
                    :switchToTeam="switchToTeam"
                    :setCanReadMessages="setCanReadMessages"
                    :readedTeams="readedTeams"
                    :invitationSent="invitationSent"
                    :invitationGot="invitationGot"
                    :newInvitation="newInvitation"
                    :stateTotalUnreadPrivateMessages="stateTotalUnreadPrivateMessages"
                    :stateTotalUnreadTopicConversations="stateTotalUnreadTopicConversations"
                    :newPendingInvitation="newPendingInvitation"
                    :temporaryLeftComponent="temporaryLeftComponent"
                    :openInvitationDeletedModal="openInvitationDeletedModal"
                    :closeInvitationDeletedModal="closeInvitationDeletedModal"
                    ref="leftSectionRef"
                />
                <add-group
                    :setNewGroups="setNewGroups"
                    :changeCurrentRightSide="changeCurrentRightSide"
                    :currentRightSide="currentRightSide"
                    :setMappedGroupFiles="setMappedGroupFiles"
                    :setMappedGroupMedia="setMappedGroupMedia"
                />
                <add-contact
                    :setNewGroups="setNewGroups"
                    :setNewGroupsCopy="setNewGroupsCopy"
                    :changeCurrentRightSide="changeCurrentRightSide"
                    :currentRightSide="currentRightSide"
                    :setNewPendingInvitation="setNewPendingInvitation"
                />
            </div>

            <div class="flex w-full h-full bg-white">
                <middle-section
                    ref="middleSectionRef"
                    :setReadedTeams="setReadedTeams"
                    :currentTopicConversations="currentTopicConversations"
                    :middleSection="middleSection"
                    :currentTopics="currentTopics"
                    :changeCurrentLeftSide="changeCurrentLeftSide"
                    :topicName="topicName"
                    :privateMessages="privateMessages"
                    :setMyNewMessages="setMyNewMessages"
                    :getLastNewPrivateMessage="getLastNewPrivateMessage"
                    :getLastNewTopicConversation="getLastNewTopicConversation"
                    :canReadMessages="canReadMessages"
                    :setCanReadMessages="setCanReadMessages"
                    :setNewGroups="setNewGroups"
                    :currentTopic="currentTopic"
                    :switchToTeam="switchToTeam"
                    :setMappedGroupFiles="setMappedGroupFiles"
                    :setMappedGroupMedia="setMappedGroupMedia"
                    :gotNewInvitation="gotNewInvitation"
                    :setTotalUnreadPrivateMessages="setTotalUnreadPrivateMessages"
                    :setTotalUnreadTopicConversations="setTotalUnreadTopicConversations"
                    :setCurrentMessageSeenBy="setCurrentMessageSeenBy"
                    :currentMessageSeenBy="currentMessageSeenBy"
                    :totalUnreadPrivateMessages="stateTotalUnreadPrivateMessages"
                    :totalUnreadTopicConversations="totalUnreadTopicConversations"
                    :changeLeftCurrentComponentOnNotificationClick="changeLeftCurrentComponentOnNotificationClick"
                    :loadingMiddleSection="loadingMiddleSection"
                    :setLoadingMiddleSection="setLoadingMiddleSection"
                    :unreadNotificationsCount="stateUnreadNotificationsCount"
                    :setUnreadNotificationsCount="setUnreadNotificationsCount"
                    :setNewNotifications="setNewNotifications"
                    :currentLeftSide="currentLeftSide"
                    :setCurrentTeamInvitations="setCurrentTeamInvitations"
                    :setInvitationsAfterAccept="setInvitationsAfterAccept"
                    :setContactsCountAfterAccept="setContactsCountAfterAccept"
                    :openInvitationDeletedModal="openInvitationDeletedModal"
                />
                <div
                    :class="currentLeftSide === 'notifications' || currentLeftSide === 'search' || currentLeftSide === 'profile' || currentLeftSide === 'group-settings' || currentLeftSide === 'messages-seen-by' ? 'rightSlideIn' : 'rightSlideOut'"
                    class="relative w-4/6 h-full"
                >
                    <div
                        v-if="currentLeftSide === 'search'"
                        class="w-full h-full">
                        <group-search :setSearchOpenedImage="setSearchOpenedImage" :changeCurrentLeftSide="changeCurrentLeftSide" :setLoadingMiddleSection="setLoadingMiddleSection" />
                    </div>
                    <div
                        v-if="currentLeftSide === 'profile'"
                        class="w-full h-full">
                        <profile :currentLeftSide="currentLeftSide"  :changeCurrentLeftSide="changeCurrentLeftSide" />
                    </div>
                    <div
                        v-if="currentLeftSide === 'group-settings'"
                        class="w-full h-full">
                        <right-section
                            :pendingGroupInvites="pendingGroupInvites"
                            :rightSideComponent="rightSideComponent"
                            :changeCurrentLeftSide="changeCurrentLeftSide"
                            :groupFiles="mappedGroupFiles"
                            :groupMedia="mappedGroupMedia"
                            :lastNewPrivateMessage="lastNewPrivateMessage"
                            :gotNewMessage="gotNewMessage"
                            :groupInvitationsSent="groupInvitationsSent"
                            :setNewGroupsCopy="setNewGroupsCopy"
                            :setGroupMediaOpenedImg="setGroupMediaOpenedImg"
                            :removeMappedTopics="removeMappedTopics"
                            :closeRightSide="closeRightSide"
                            :removeCurrentTopicAfterDelete="removeCurrentTopicAfterDelete"
                            ref="rightSectionRef"
                        />
                    </div>
                    <div v-if="currentLeftSide === 'messages-seen-by' && currentMessageSeenBy" class="w-full h-full">
                        <messages-seen-by
                            :setCurrentMessageSeenBy="setCurrentMessageSeenBy"
                            :changeCurrentLeftSide="changeCurrentLeftSide"
                            :currentMessageSeenBy="currentMessageSeenBy"
                            :loadingMessageSeenBy="loadingMessageSeenBy"
                        />
                    </div>
<!--                    <div v-if="currentLeftSide === 'notifications'" class="w-full h-full">-->
<!--                        <notifications-->
<!--                            :changeCurrentLeftSide="changeCurrentLeftSide"-->
<!--                            :unsetUnreadNotificationsCount="unsetUnreadNotificationsCount"-->
<!--                            :clearNotifications="clearNotifications"-->
<!--                            ref="notificationsRef"-->
<!--                        />-->
<!--                    </div>-->
                </div>

            </div>

            <div style="z-index: 100" class="fixed top-0 bottom-0 flex items-center justify-center left-0 right-0 w-full h-screen bg-gray-700 bg-opacity-90" v-if="openSearchImg && searchOpenedImage">
                <div class="p-12 flex items-center justify-center w-full h-full">
                    <img
                        :src="`/image-get/${searchOpenedImage.filename}`"
                        alt="Image"
                        v-if="searchOpenedImage.extension === 'jfif' || searchOpenedImage.extension === 'jpg' || searchOpenedImage.extension === 'jpeg' || searchOpenedImage.extension === 'png'"
                        class="w-full h-full rounded-md openedImg"
                    >
                </div>

                <button @click="closingSearchImage" type="button" class="absolute p-2 text-gray-400 rounded-full top-5 right-5 hover:text-gray-600 hover:bg-gray-300 focus:outline-none focus:ring">
                    <svg class="w-6 h-6 fill-current" viewBox="0 0 20 20">
                        <path d="M15.898,4.045c-0.271-0.272-0.713-0.272-0.986,0l-4.71,4.711L5.493,4.045c-0.272-0.272-0.714-0.272-0.986,0s-0.272,0.714,0,0.986l4.709,4.711l-4.71,4.711c-0.272,0.271-0.272,0.713,0,0.986c0.136,0.136,0.314,0.203,0.492,0.203c0.179,0,0.357-0.067,0.493-0.203l4.711-4.711l4.71,4.711c0.137,0.136,0.314,0.203,0.494,0.203c0.178,0,0.355-0.067,0.492-0.203c0.273-0.273,0.273-0.715,0-0.986l-4.711-4.711l4.711-4.711C16.172,4.759,16.172,4.317,15.898,4.045z"></path>
                    </svg>
                </button>
            </div>

            <image-fullscreen
                v-if="groupMediaOpenedImg"
                :imageToOpen="groupMediaOpenedImg"
                :closingImage="closeGroupMediaOpenedImg"
            />


        <!-- Invitation was deleted modal -->
        <div v-if="showInvitationDeletedModal" class="fixed invitationDeletedPosition top-0 left-0 right-0 bottom-0 w-full h-screen bg-gray-700 bg-opacity-90 flex items-center justify-center">
            <div class="md:w-1/3 sm:w-full rounded-lg shadow-lg bg-white my-3">
                <div class="flex justify-between border-b border-gray-100 px-5 py-4">
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-6 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <span class="font-bold ml-2 text-gray-700 text-lg">Error</span>
                    </div>
                    <div>
                        <button @click="closeInvitationDeletedModal">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="px-10 py-5 text-gray-600">
                    {{ errorModalMessage }}
                </div>

                <div class="px-5 py-4 flex justify-end">
                    <button @click="closeInvitationDeletedModal" class="text-sm py-2 px-3 text-gray-500 hover:text-gray-600 transition duration-150">OK</button>
                </div>
            </div>
        </div>

        <!-- Error Modal -->
        <div v-if="$page.props.flash.error" class="fixed invitationDeletedPosition top-0 left-0 right-0 bottom-0 w-full h-screen bg-gray-700 bg-opacity-90 flex items-center justify-center">
            <div class="md:w-1/3 sm:w-full rounded-lg shadow-lg bg-white my-3">
                <div class="flex justify-between border-b border-gray-100 px-5 py-4">
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-6 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <span class="font-bold ml-2 text-gray-700 text-lg">Error</span>
                    </div>
                    <div>
                        <button @click="closeInvitationDeletedModal">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="px-10 py-5 text-gray-600">
                    {{ $page.props.flash.error }}
                </div>

                <div class="px-5 py-4 flex justify-end">
                    <button @click="closeErrorModal" class="text-sm py-2 px-3 text-gray-500 hover:text-gray-600 transition duration-150">OK</button>
                </div>
            </div>
        </div>

        </section>
    </div>
</template>

<script>
import LeftSection from "@/Components/LeftSection";
import MiddleSection from "@/Components/MiddleSection";
import RightSection from "@/Components/RightSection";
import AddGroup from "@/Components/AddGroup";
import AddContact from "@/Components/AddContact";
import Profile from "@/Components/Profile";
import GroupSearch from "@/Components/GroupSearch";
import MessagesSeenBy from "@/Components/MessagesSeenBy";
import Notifications from "@/Components/Notifications";
import ImageFullscreen from "@/Components/ImageFullscreen";

export default {
    components: {
        Profile,
        RightSection,
        MiddleSection,
        LeftSection,
        AddGroup,
        AddContact,
        GroupSearch,
        MessagesSeenBy,
        Notifications,
        ImageFullscreen
    },
    props: {
        canLogin: Boolean,
        canRegister: Boolean,
        rightSide: String,
        currentTopics: Array,
        currentTopicConversations: Array,
        middleSection: String,
        rightSideComponent: '',
        pendingGroupInvites: Array,
        topicName: String,
        groupFiles: Array,
        privateMessages: Array,
        groupMedia: Array,
        currentTopic: Object,
        teams: Object,
        invitationGot: Array,
        invitationSent: Array,
        groupInvitationsSent: Array,
        totalUnreadPrivateMessages: Number,
        totalUnreadTopicConversations: Number,
        unreadNotificationsCount: Number
    },
    data() {
        return {
            currentRightSide: 'groups',
            currentLeftSide: '',
            team_id: null,
            lastNewPrivateMessage: null,
            lastNewTopicConversation: null,
            gotNewMessage: null,
            newGroups: [],
            canReadMessages: false,
            readedTeams: [],
            mappedGroupMedia: [],
            mappedGroupFiles: [],
            newInvitation: null,
            stateTotalUnreadPrivateMessages: null,
            stateTotalUnreadTopicConversations: null,
            newPendingInvitation: null,
            currentMessageSeenBy: null,
            loadingMessageSeenBy: false,
            temporaryLeftComponent: null,
            loadingMiddleSection: false,
            stateUnreadNotificationsCount: 0,
            windowEventListenerAttached: false,
            searchOpenedImage: null,
            openSearchImg: false,
            groupMediaOpenedImg: null,
            showInvitationDeletedModal: false,
            invitationDeletedId: null,
            errorModalMessage: "",
            showErrorModal: false
        }
    },
    methods: {
        closeErrorModal() {
            this.showErrorModal = false;
        },
        removeMappedTopics() {
            this.$refs.middleSectionRef.mappedCurrentTopics = [];
        },
        openInvitationDeletedModal(invitationId, message) {
            if (invitationId) {
                this.invitationDeletedId = invitationId;
            }
            this.showInvitationDeletedModal = true;
            this.errorModalMessage = message;
        },
        closeInvitationDeletedModal() {
            this.showInvitationDeletedModal = false;
            this.$refs.leftSectionRef.removeDeletedInvitation(this.invitationDeletedId);
        },
        setContactsCountAfterAccept(teams) {
              this.$refs.leftSectionRef.setContactsCount(teams);
        },
        setInvitationsAfterAccept(invitations) {
            this.$refs.leftSectionRef.mappedPendingInvitations = invitations;
        },
        setCurrentTeamInvitations(invitations) {
            this.$refs.rightSectionRef.mappedTeamInvitations = invitations;
        },
        setGroupMediaOpenedImg(img) {
            this.groupMediaOpenedImg = img;
        },
        closeGroupMediaOpenedImg() {
            this.groupMediaOpenedImg = null;
        },
        setSearchOpenedImage(img) {
            this.searchOpenedImage = img;
            this.openSearchImg = true
        },
        closingSearchImage() {
            this.searchOpenedImage = null;
            this.openSearchImg = false
        },
        changeLeftCurrentComponentOnNotificationClick(component) {
            this.temporaryLeftComponent = component;
        },
        changeCurrentRightSide(component) {
            this.currentRightSide = component;
            this.$refs.leftSectionRef.currentComponent = component;
        },
        switchToTeam(teamId) {
            this.loadingMiddleSection = true;
            this.$inertia.put(route('custom.sw'), {
                'team_id': teamId
            }, {
                preserveState: false,
                onSuccess: (res) => {
                    // this.$refs.middleSectionRef.mappedCurrentTopics = res.props.currentTopics;
                    // this.$refs.middleSectionRef.mappedPrivateMessages = res.props.privateMessages;
                    // this.$refs.middleSectionRef.stateCurrentTopic = res.props.currentTopic;
                    this.loadingMiddleSection = false;
                },
            })
        },
        setLoadingMiddleSection(status) {
            this.loadingMiddleSection = status;
        },
        closeRightSide() {
            this.currentLeftSide = "profile";
        },
        changeCurrentLeftSide(component) {
            if (component !== 'messages-seen-by') {
                localStorage.setItem('cr_side', component);
            }
            this.currentLeftSide = component;
        },
        checkLastLeftSide() {
            let component = localStorage.getItem('cr_side');
            if (component) {
                if (this.$page.props.user.current_team) {
                    this.currentLeftSide = component;
                } else {
                    localStorage.setItem('cr_side', '')
                    this.currentLeftSide = "";
                }
            } else {
                localStorage.setItem('cr_side', '')
                this.currentLeftSide = "";
            }
        },
        setMyNewMessages(message) {
            this.gotNewMessage = message;
        },
        setNewGroups(groups) {
            this.newGroups = Object.values(groups);
        },
        getLastNewPrivateMessage(message) {
            this.lastNewPrivateMessage = message;
        },
        getLastNewTopicConversation(message) {
            this.lastNewPrivateMessage = message;
        },
        setCanReadMessages(value) {
            this.canReadMessages = value;
        },
        setReadedTeams(teams) {
            this.readedTeams = teams;
        },
        setMappedGroupFiles(files) {
            this.mappedGroupFiles = files;
        },
        setMappedGroupMedia(medias) {
            this.mappedGroupMedia = medias;
        },
        gotNewInvitation(invitation) {
            this.newInvitation = invitation;
        },
        setNewGroupsCopy(groups) {
            this.newGroups = groups;
        },
        setTotalUnreadPrivateMessages(count) {
            this.stateTotalUnreadPrivateMessages = count;
        },
        setTotalUnreadTopicConversations(count) {
            this.stateTotalUnreadTopicConversations = count;
        },
        setNewPendingInvitation(invitation) {
            this.newPendingInvitation = invitation;
        },
        setCurrentMessageSeenBy(message) {
            this.loadingMessageSeenBy = true;
            this.currentMessageSeenBy = message;
            this.getMessageSeenBy(message);
        },
        setCurrentMessageSeenByAfter(message) {
            this.currentMessageSeenBy = message;
        },
        getMessageSeenBy(message) {
            if (message) {
                if (this.$page.props.user.current_team.team_type === 'group') {
                    window.axios.get(`/get-topic-message-seen-by?tcid=${message.id}&tid=${message.topic_id}`).then(res => {
                        this.setCurrentMessageSeenByAfter(res.data);
                        this.loadingMessageSeenBy = false;
                    });
                }
                if (this.$page.props.user.current_team.team_type === 'private-message') {
                    window.axios.get(`/get-private-message-seen-by?pmid=${message.id}`).then(res => {
                        this.setCurrentMessageSeenByAfter(res.data);
                        this.loadingMessageSeenBy = false;
                    });
                }
            }
        },
        unsetUnreadNotificationsCount() {
            this.stateUnreadNotificationsCount = 0;
        },
        setUnreadNotificationsCount() {
            this.stateUnreadNotificationsCount++;
        },
        clearNotifications() {
            this.$refs.middleSectionRef.newNotifications = [];
        },
        setNewNotifications(notification){
            if (this.currentLeftSide === 'notifications') {
                this.$refs.notificationsRef.mappedTotalNotifications = [notification, ...this.$refs.notificationsRef.mappedTotalNotifications];
            };
        },
        removeCurrentTopicAfterDelete(){
            this.$refs.middleSectionRef.stateCurrentTopic = null;
        }
    },
    mounted() {
        const esn = localStorage.getItem('esn');
        const ebn = localStorage.getItem('ebn');
        if (!esn) localStorage.setItem('esn', 'enabled');
        if (!ebn) localStorage.setItem('ebn', 'enabled');


        if (this.rightSide) {
            this.currentRightSide = this.rightSide;
        }
        this.stateUnreadNotificationsCount = this.unreadNotificationsCount;
        this.checkLastLeftSide();
        this.mappedGroupFiles = this.groupFiles;
        this.mappedGroupMedia = this.groupMedia;
        this.stateTotalUnreadPrivateMessages = this.totalUnreadPrivateMessages;
        this.stateTotalUnreadTopicConversations = this.totalUnreadTopicConversations;
    },
    watch: {
        teams: {
            immediate: true,
            deep: true,
            handler(newValue, oldValue) {
                if (newValue) {
                    this.setNewGroups(newValue)
                }
            }
        }
    }
}
</script>

<style scoped>
    section {
        height: 100vh;
        max-height: 100vh;
    }
    .rightSlideIn {
        transform: translateX(0);
        transition: .5s;
    }
    .rightSlideOut {
        transform: translateX(100%);
        width: 0;
        opacity: .5;
        transition: .5s;
    }
    .openedImg {
        height: auto;
        max-height: 100%;
        width: auto;
        object-position: center;
        object-fit: cover;
    }
    .invitationDeletedPosition {
        z-index: 101;
    }
</style>


