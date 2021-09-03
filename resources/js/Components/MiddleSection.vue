<template>
    <div class="flex flex-col items-stretch relative justify-start w-full overflow-y-hidden bg-white border-l-2 border-r-2 border-gray-100">
        <!-- Header with name -->
        <div  class="flex flex-row items-center justify-between px-3 py-2 border-b-2 border-gray-100 bg-opacity-40">
            <div v-if="$page.props.user.current_team && $page.props.user.current_team.name !== 'not-showing' && $page.props.user.current_team.team_type === 'group'" class="flex items-center">
<!--                <inertia-link v-show="middleSection === 'topic-conversations'" :href="route('home')" :only="['middleSection', 'currentTopics', 'teams']">-->
<!--                    <button type="button" class="p-2 mr-2 text-gray-400 rounded-full hover:text-gray-600 hover:bg-gray-300 focus:outline-none">-->
<!--                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">-->
<!--                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" />-->
<!--                        </svg>-->
<!--                    </button>-->
<!--                </inertia-link>-->
                <button @click="switchToTeam($page.props.user.current_team_id)" v-if="middleSection === 'topic-conversations'"  type="button" class="p-2 mr-2 text-gray-400 rounded-full hover:text-gray-600 hover:bg-gray-300 focus:outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
                    </svg>
                </button>
                <h2 class="flex items-center text-sm">
                    <img
                        class="object-cover w-10 h-10 mr-2 bg-gray-800 rounded-full shadow-lg"
                        :class="!$page.props.user.current_team.profile_photo_path && 'contrast'"
                        :src="$page.props.user.current_team.profile_photo_path ? '/get-profile-photos/'+$page.props.user.current_team.profile_photo_path : '/group.png'"
                        alt="user img"
                    >
                    <span class="font-semibold text-gray-600">
                        {{ $page.props.user.current_team.name }}
                    </span>
                    <inertia-link v-if="middleSection === 'topic-conversations' && currentTopic" :href="route('home')" :only="['middleSection', 'currentTopic']">
                        <span class="font-semibold text-blue-400">
                            <span class="text-gray-500"> &nbsp;~&nbsp; </span>
                            <span>{{ currentTopic.title }}</span>
                        </span>
                    </inertia-link>

                </h2>
            </div>

            <div v-else-if="$page.props.user.current_team && $page.props.user.current_team.team_type === 'private-message'">
                <span class="text-sm font-semibold test-gray-600">
                    <span v-if="$page.props.user.id === $page.props.user.current_team.user_id" class="relative flex items-center">
                        <img
                            class="object-cover w-10 h-10 mr-2 bg-gray-800 rounded-full shadow-lg"
                            :class="$page.props.user.current_team.receiver && !$page.props.user.current_team.receiver.profile_photo_path && 'contrast'"
                            :src="$page.props.user.current_team.receiver && $page.props.user.current_team.receiver.profile_photo_path ? '/get-profile-photos/'+$page.props.user.current_team.receiver.profile_photo_path : '/user.png'"
                            alt="user img"
                        >
                        {{ $page.props.user.current_team.receiver.name }}
                        <span v-if="$page.props.user.current_team.receiver.status === 'online'" class="absolute bottom-0 w-4 h-4 bg-green-400 rounded-full left-8"></span>
                    </span>
                    <span v-if="$page.props.user.id === $page.props.user.current_team.receiver_id" class="relative flex items-center">
                        <img
                            class="object-cover w-10 h-10 mr-2 bg-gray-800 rounded-full"
                            :class="!$page.props.user.current_team.owner.profile_photo_path && 'contrast'"
                            :src="$page.props.user.current_team.owner.profile_photo_path ? '/get-profile-photos/'+$page.props.user.current_team.owner.profile_photo_path : '/user.png'"
                            alt="user img"
                        >
                        {{ $page.props.user.current_team.owner.name }}
                        <span v-if="$page.props.user.current_team.owner.status === 'online'" class="absolute bottom-0 w-4 h-4 bg-green-400 rounded-full left-8"></span>
                    </span>
                </span>
            </div>

            <div v-else class="flex"></div>

            <div class="flex flex-row">

                <button
                    v-show="middleSection === 'topics' && $page.props.user.current_team && $page.props.user.current_team.team_type === 'group' && $page.props.user.current_team.name !== 'not-showing'"
                    @click="openShowAddTopicForm"
                    type="button"
                    class="p-2 ml-2 relative text-gray-400 has-tooltip rounded-full hover:text-gray-600 hover:bg-gray-100 focus:outline-none"
                    aria-label="Search"
                >
                    <span class='tooltip right-4 -bottom-7 w-32 z-50 text-xs rounded-lg shadow-lg  p-2 text-white bg-gray-500'>Add new Topic
                       <svg class="z-40 w-4 h-4 absolute right-2 rotate-180 -top-1.5 text-gray-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M17.707 9.293a1 1 0 010 1.414l-7 7a1 1 0 01-1.414 0l-7-7A.997.997 0 012 10V5a3 3 0 013-3h5c.256 0 .512.098.707.293l7 7zM5 6a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path></svg>
                    </span>

                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                </button>



                <button v-if="$page.props.user.current_team && $page.props.user.current_team.name !== 'not-showing'" @click="changeCurrentLeftSide('search')" type="button" class="p-2 has-tooltip relative ml-2 text-gray-400 rounded-full hover:text-gray-600 hover:bg-gray-100 focus:outline-none" aria-label="Search">
                    <svg class="w-6 h-6 fill-current" viewBox="0 0 20 20">
                        <path d="M12.323,2.398c-0.741-0.312-1.523-0.472-2.319-0.472c-2.394,0-4.544,1.423-5.476,3.625C3.907,7.013,3.896,8.629,4.49,10.102c0.528,1.304,1.494,2.333,2.72,2.99L5.467,17.33c-0.113,0.273,0.018,0.59,0.292,0.703c0.068,0.027,0.137,0.041,0.206,0.041c0.211,0,0.412-0.127,0.498-0.334l1.74-4.23c0.583,0.186,1.18,0.309,1.795,0.309c2.394,0,4.544-1.424,5.478-3.629C16.755,7.173,15.342,3.68,12.323,2.398z M14.488,9.77c-0.769,1.807-2.529,2.975-4.49,2.975c-0.651,0-1.291-0.131-1.897-0.387c-0.002-0.004-0.002-0.004-0.002-0.004c-0.003,0-0.003,0-0.003,0s0,0,0,0c-1.195-0.508-2.121-1.452-2.607-2.656c-0.489-1.205-0.477-2.53,0.03-3.727c0.764-1.805,2.525-2.969,4.487-2.969c0.651,0,1.292,0.129,1.898,0.386C14.374,4.438,15.533,7.3,14.488,9.77z"></path>
                    </svg>

                    <span class='tooltip right-4 -bottom-7 w-32 z-50 text-xs rounded-lg shadow-lg  p-2 text-white bg-gray-500'>Search
                       <svg class="z-40 w-4 h-4 absolute right-2 rotate-180 -top-1.5 text-gray-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M17.707 9.293a1 1 0 010 1.414l-7 7a1 1 0 01-1.414 0l-7-7A.997.997 0 012 10V5a3 3 0 013-3h5c.256 0 .512.098.707.293l7 7zM5 6a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path></svg>
                    </span>
                </button>

<!--                <button @click="changeCurrentLeftSide('notifications')" type="button" class="p-2 relative ml-2 text-gray-400 has-tooltip relative rounded-full hover:text-gray-600 hover:bg-gray-100 focus:outline-none" aria-label="Search">-->
<!--                         <span class='tooltip right-4 -bottom-7 w-32 z-50 text-xs rounded-lg shadow-lg  p-2 text-white bg-gray-500'>-->
<!--                             Notifications-->
<!--                       <svg class="z-40 w-4 h-4 absolute right-2 rotate-180 -top-1.5 text-gray-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M17.707 9.293a1 1 0 010 1.414l-7 7a1 1 0 01-1.414 0l-7-7A.997.997 0 012 10V5a3 3 0 013-3h5c.256 0 .512.098.707.293l7 7zM5 6a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path></svg>-->
<!--                    </span>-->
<!--                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>-->
<!--                    <span v-if="unreadNotificationsCount && unreadNotificationsCount > 0" class="absolute px-2 py-1 text-xs text-white bg-blue-400 rounded-full -top-1 right-1">-->
<!--                        {{unreadNotificationsCount}}-->
<!--                    </span>-->
<!--                </button>-->

                <button @click="changeCurrentLeftSide('profile')" type="button" class="p-2 ml-2 text-gray-400 has-tooltip relative rounded-full hover:text-gray-600 hover:bg-gray-100 focus:outline-none" aria-label="Open">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z" clip-rule="evenodd" />
                    </svg>
                    <span class='tooltip right-4 -bottom-7 w-32 z-50 text-xs rounded-lg shadow-lg  p-2 text-white bg-gray-500'>Profile
                       <svg class="z-40 w-4 h-4 absolute right-2 rotate-180 -top-1.5 text-gray-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M17.707 9.293a1 1 0 010 1.414l-7 7a1 1 0 01-1.414 0l-7-7A.997.997 0 012 10V5a3 3 0 013-3h5c.256 0 .512.098.707.293l7 7zM5 6a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path></svg>
                    </span>
                </button>
                <button v-if="$page.props.user.current_team && $page.props.user.current_team.name !== 'not-showing'" @click="changeCurrentLeftSide('group-settings')" type="button" class="has-tooltip relative p-2 ml-2 text-gray-400 rounded-full hover:text-gray-600 hover:bg-gray-100 focus:outline-none" aria-label="More">
                    <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24">
                        <path fill-rule="nonzero" d="M12,16 C13.1045695,16 14,16.8954305 14,18 C14,19.1045695 13.1045695,20 12,20 C10.8954305,20 10,19.1045695 10,18 C10,16.8954305 10.8954305,16 12,16 Z M12,10 C13.1045695,10 14,10.8954305 14,12 C14,13.1045695 13.1045695,14 12,14 C10.8954305,14 10,13.1045695 10,12 C10,10.8954305 10.8954305,10 12,10 Z M12,4 C13.1045695,4 14,4.8954305 14,6 C14,7.1045695 13.1045695,8 12,8 C10.8954305,8 10,7.1045695 10,6 C10,4.8954305 10.8954305,4 12,4 Z"/>
                    </svg>
                    <span class='tooltip right-4 -bottom-7 w-32 z-50 text-xs rounded-lg shadow-lg  p-2 text-white bg-gray-500'>Group Details
                       <svg class="z-40 w-4 h-4 absolute right-2 rotate-180 -top-1.5 text-gray-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M17.707 9.293a1 1 0 010 1.414l-7 7a1 1 0 01-1.414 0l-7-7A.997.997 0 012 10V5a3 3 0 013-3h5c.256 0 .512.098.707.293l7 7zM5 6a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path></svg>
                    </span>
                </button>
            </div>
        </div>

            <jet-dialog-modal :show="showAddTopicForm" @close="closeShowAddTopicForm">
                <template #title>
                    <p v-if="topicForm.update" class="text-gray-500">Edit Topic</p>
                    <p v-else class="text-gray-500">Create New Topic</p>
                </template>
                <template #content>
                    <!-- Add Topic Form -->
                    <form v-if="!topicForm.update"  @submit.prevent="postTopic($page.props.user.current_team_id)"
                           class="flex flex-row items-center justify-between"
                    >
                        <div class="flex flex-col flex-1">
                            <input v-model="topicForm.title" class="p-2 mb-4 text-sm border-gray-300 bg-gray-100 border  rounded-md outline-none focus:ring-transparent focus:border-gray-500 title" spellcheck="false" placeholder="Title" type="text">
                            <textarea v-model="topicForm.description"
                                      class="h-auto p-3 text-sm bg-gray-100 border border-gray-300 rounded-md outline-none resize-none-transparent focus:ring-transparent focus:border-gray-500 description sec"
                                      spellcheck="false"
                                      placeholder="Describe everything about this topic here (Optional)"
                            ></textarea>
                            <select v-model="topicForm.permission" name="topic-permission" id="tpermission"
                                    class="p-2 mb-4 text-sm bg-gray-100 border rounded-md outline-none border-gray-300 focus:border-gray-500 focus:ring-transparent title mt-5"
                            >
                                <option disabled>Permission</option>
                                <option value="readonly">Readonly</option>
                                <option value="public">Public</option>
                                <option value="custom">Custom</option>
                            </select>
                            <span v-if="topicForm.permission === 'custom'" class="text-gray-500 mb-2 text-xs">Select permission for every user from this group.</span>
                            <ul v-if="topicForm.permission === 'custom'" class="overflow-y-auto h-72 pr-3 customScroll">
                                <topic-permission-user
                                    v-for="user in [...$page.props.user.current_team.users, $page.props.user.current_team.owner]"
                                    :user="user"
                                    @permission:updated="handleTopicPermissionUser"
                                />
                            </ul>
                        </div>
                    </form>

                    <!-- Edit Topic From -->
                    <form
                        @submit.prevent="editingTopic($page.props.user.current_team_id)"
                        v-if="showAddTopicForm && $page.props.user.current_team && $page.props.user.current_team.team_type === 'group' && topicForm.update"
                        class="flex flex-row items-center justify-between">

                        <div class="flex flex-col flex-1">
                            <input v-model="topicForm.title" class="p-2 mb-4 text-sm border-gray-300 bg-gray-100 border  rounded-md outline-none focus:ring-transparent focus:border-gray-500 title" spellcheck="false" placeholder="Title" type="text">
                            <textarea v-model="topicForm.description"
                                      class="h-auto p-3 text-sm bg-gray-100 border border-gray-300 rounded-md outline-none resize-none-transparent focus:ring-transparent focus:border-gray-500 description sec"
                                      spellcheck="false"
                                      placeholder="Describe everything about this topic here (Optional)"
                            ></textarea>
                            <select v-model="topicForm.permission" name="topic-permission" id="tepermission"
                                    class="p-2 mb-4 text-sm bg-gray-100 border rounded-md outline-none border-gray-300 focus:border-gray-500 focus:ring-transparent title mt-5"
                            >
                                <option value="readonly">Readonly</option>
                                <option value="public">Public</option>
                                <option value="custom">Custom</option>
                            </select>
                            <span v-if="topicForm.permission === 'custom'" class="text-gray-500 mb-2 text-xs">Select permission for every user from this group.</span>
                            <ul v-if="topicForm.permission === 'custom'" class="overflow-y-auto h-72 customScroll">
                                <ul v-if="topicForm.permission === 'custom'" class="overflow-y-auto h-72 px-3 customScroll">
                                    <topic-permission-user
                                        v-for="user in [...$page.props.user.current_team.users, $page.props.user.current_team.owner]"
                                        :user="user"
                                        @permission:updated="handleTopicPermissionUser"
                                        :permission="findUserPermissionWhenEditTopic(user.id)"
                                    />
                                </ul>
                            </ul>
                        </div>
                    </form>

                </template>
                <template #footer>
                    <div v-if="!topicForm.update" class="flex items-center justify-end w-full my-3">
                        <button @click="closeShowAddTopicForm" type="button" class="px-5 py-2 mr-2 text-sm text-white bg-gray-400 rounded-md focus:outline-none hover:bg-gray-500 hover:shadow-lg">Cancel</button>
                        <button type="submit" @click.prevent="postTopic($page.props.user.current_team_id)" class="px-5 py-2 text-sm text-white bg-blue-500 rounded-md focus:outline-none hover:bg-blue-600 hover:shadow-lg">Post</button>
                    </div>
                    <div v-if="topicForm.update" class="flex items-center justify-end w-full my-3">
                        <button @click="closeShowAddTopicForm" type="button" class="px-5 py-2 mr-2 text-sm text-white bg-gray-400 rounded-md focus:outline-none hover:bg-gray-500 hover:shadow-lg">Cancel</button>
                        <button @click.prevent="editingTopic($page.props.user.current_team_id)" type="submit" class="px-5 py-2 text-sm text-white bg-blue-500 rounded-md focus:outline-none hover:bg-blue-600 hover:shadow-lg">Save</button>
                    </div>
                </template>
            </jet-dialog-modal>


            <div v-if="loadingMiddleSection" class="flex items-center absolute spinnerPosition justify-center w-full h-full">
                <loading-spinner />
            </div>

            <div
                id="topicsScrollingArea"
                v-if="!loadingMiddleSection && currentMiddleSection === 'topics' && $page.props.user.current_team && $page.props.user.current_team.team_type === 'group'"
                class="flex-grow overflow-x-hidden overflow-y-auto bg-gray-50 customScroll">
                <messages
                    v-if="$page.props.user.current_team.team_type === 'group' && !$page.props.user.current_team.is_deleted"
                    :setMappedCurrentTopics="setMappedCurrentTopics"
                    :editTopic="editTopic"
                    :setCurrentTopic="setCurrentTopic"
                    :currentTopics="mappedCurrentTopics"
                    :changeCurrentMiddleSection="changeCurrentMiddleSection"
                    :setLoadingMiddleSection="setLoadingMiddleSection"
                    :loadingTopicsPaginate="loadingTopicsPaginate"
                    :topicConversationScroll="topicConversationScroll"
                />
                <div v-if="$page.props.user.current_team.team_type === 'group' && $page.props.user.current_team.is_deleted">
                    <p class="text-gray-600 text-bold text-xl w-full text-center mt-32">This group was deleted by the administrator.</p>
                </div>
            </div>

        <div v-show="!loadingMiddleSection && currentMiddleSection === 'topic-conversations' && $page.props.user.current_team && $page.props.user.current_team.team_type === 'group'"
            class="flex-grow w-full h-full overflow-x-hidden overflow-y-auto bg-gray-100 customScroll"
            id="topicScrollingArea"
        >
            <topic-conversations
                :currentTopicConversations="mappedCurrentTopicConversations"
                :changeCurrentMiddleSection="changeCurrentMiddleSection"
                :getNewTopicConversation="getNewTopicConversation"
                :setNewGroups="setNewGroups"
                :changeCurrentLeftSide="changeCurrentLeftSide"
                :setTotalUnreadPrivateMessages="setTotalUnreadPrivateMessages"
                :setTotalUnreadTopicConversations="setTotalUnreadTopicConversations"
                :setCurrentMessageSeenBy="setCurrentMessageSeenBy"
                :currentTopic="stateCurrentTopic"
                :totalUnreadTopicConversations="totalUnreadTopicConversations"
                :lazyLoadingTopicConversations="lazyLoadingTopicConversations"
                :sendingTopicConversation="sendingTopicConversation"
                ref="topicConversationsRef"
            />
        </div>

        <div v-show="!loadingMiddleSection && $page.props.user.current_team && $page.props.user.current_team.team_type === 'private-message'"
            class="flex-grow w-full h-full overflow-x-hidden overflow-y-auto bg-gray-100 customScroll"
            id="privateScrollingArea"
        >
            <private-conversations
                :canReadMessages="canReadMessages"
                :changeCurrentLeftSide="changeCurrentLeftSide"
                :privateMessages="mappedPrivateMessages"
                :getNewMessage="getNewMessage"
                :setCanReadMessages="setCanReadMessages"
                :setReadedTeams="setReadedTeams"
                :setNewGroups="setNewGroups"
                :setTotalUnreadPrivateMessages="setTotalUnreadPrivateMessages"
                :setTotalUnreadTopicConversations="setTotalUnreadTopicConversations"
                :setCurrentMessageSeenBy="setCurrentMessageSeenBy"
                :totalUnreadPrivateMessages="totalUnreadPrivateMessages"
                :setMappedCurrentPrivateMessages="setMappedCurrentPrivateMessages"
                :lazyLoadingPrivateMessages="lazyLoadingPrivateMessages"
                :setLazyLoadingPrivateMessages="setLazyLoadingPrivateMessages"
                :sendingPrivateMessage="sendingPrivateMessage"
                :setMessagePrivate="setMessagePrivate"
                ref="privateConversationsRef"
            />
        </div>

        <form  v-if="currentTopic && currentTopic.userPermission === 'public' && !loadingMiddleSection && currentMiddleSection === 'topic-conversations' && $page.props.user.current_team.team_type === 'group'"
               @submit.prevent="writeMessage"
               class="flex flex-col relative p-3 border-t-2 border-gray-200"
        >
            <gif-search
                v-if="showGifSearchTopic"
                :showGifSearch="showGifSearchTopic"
                :closeGifSearch="closeGifSearchTopic"
                :setMessagePrivate="setMessageTopic"
                :writeMessagePrivate="writeMessage"
            />

            <div v-if="topicOpenedFiles && topicOpenedFiles.length > 0" class="w-full px-16 pb-5">
                <div class="flex flex-wrap w-full">
                    <div v-for="(file, i) in topicOpenedFiles" :key="i" class="flex items-end p-1 m-1 bg-blue-100 rounded-md shadow">
                        <div class="flex flex-col justify-center mt-3 text-xs font-semibold">
                            <div v-if="getFileExtension(file) === 'mp4'" class="bg-gray-900">
                                <video style="width: 20rem; height: 20rem;" controls>
                                    <source :src="getImgPath(file)" />
                                </video>
                            </div>
                            <img v-if="getFileExtension(file) === 'pdf'" src="/pdf.png" alt="pdf" class="w-8 h-8 mr-2" />
                            <img v-if="getFileExtension(file) === 'xlsx'" src="/excel.png" alt="Excell" class="w-8 h-8 mr-2" />
                            <img v-if="getFileExtension(file) === 'doc'" src="/word.png" alt="Word" class="w-8 h-8 mr-2" />

                            <img
                                v-if="getFileExtension(file) === 'jfif' || getFileExtension(file) === 'jpg' || getFileExtension(file) === 'jpeg' || getFileExtension(file) === 'png'"
                                :src="getImgPath(file)"
                                alt="No Image :("
                                class="w-24 h-24 mr-2 rounded-md shadow"
                            >
                            <img v-if="getFileExtension(file) === 'rtf' || getFileExtension(file) === 'txt'" src="/doc.png" alt="Document" class="w-8 h-8 mr-2" />
                            <span class="text-xs text-gray-600">{{ file.name }}</span>
                        </div>
                        <button @click="removeTopicConversationFile(i)" type="button" class="p-1.5 ml-2 text-gray-400 rounded-full hover:text-gray-600 bg-gray-300 focus:outline-none">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </div>
                </div>
            </div>
            <div class="flex items-center w-full">
                <div class="">
                    <button @click="onPickFile" type="button" class="p-2 text-gray-400 rounded-full hover:text-gray-600 hover:bg-gray-100 focus:outline-none" aria-label="Upload a files">
                        <svg class="w-6 h-6 fill-current" viewBox="0 0 20 20">
                            <path d="M4.317,16.411c-1.423-1.423-1.423-3.737,0-5.16l8.075-7.984c0.994-0.996,2.613-0.996,3.611,0.001C17,4.264,17,5.884,16.004,6.88l-8.075,7.984c-0.568,0.568-1.493,0.569-2.063-0.001c-0.569-0.569-0.569-1.495,0-2.064L9.93,8.828c0.145-0.141,0.376-0.139,0.517,0.005c0.141,0.144,0.139,0.375-0.006,0.516l-4.062,3.968c-0.282,0.282-0.282,0.745,0.003,1.03c0.285,0.284,0.747,0.284,1.032,0l8.074-7.985c0.711-0.71,0.711-1.868-0.002-2.579c-0.711-0.712-1.867-0.712-2.58,0l-8.074,7.984c-1.137,1.137-1.137,2.988,0.001,4.127c1.14,1.14,2.989,1.14,4.129,0l6.989-6.896c0.143-0.142,0.375-0.14,0.516,0.003c0.143,0.143,0.141,0.374-0.002,0.516l-6.988,6.895C8.054,17.836,5.743,17.836,4.317,16.411"></path>
                        </svg>
                    </button>
                    <input
                        v-on:change="topicConversationFile"
                        type="file"
                        style="display: none"
                        ref="fileInput"
                        @change="onFilePicked"/>
                </div>
                <div class="flex items-center flex-1 px-3">
                    <button v-if="!showEmojisTopic" @click="openEmojisTopic" type="button" class="p-2 text-gray-400 rounded-full hover:text-gray-600 hover:bg-gray-100 focus:outline-none" aria-label="Upload a files">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </button>
                    <button v-if="showEmojisTopic" @click="closeEmojisTopic" type="button" class="p-2 text-gray-400 rounded-full hover:text-gray-600 hover:bg-gray-100 focus:outline-none" aria-label="Upload a files">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-6" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <img @click="openGifSearchTopic" src="/gificon.png" class="w-8 h-8 cursor-pointer" alt="...">
                    <VuemojiPicker v-if="showEmojisTopic" class="absolute light emojiPosition" @emojiClick="handleEmojiClickTopic" />
                    <input
                        ref="messageInput"
                        v-model.lazy="topicConversationMessage"
                        type="text"
                        placeholder="Write your message ..."
                        class="w-full leading-7 py-2 pl-2 ml-2 text-sm text-gray-700 placeholder-gray-500 bg-gray-600 border-gray-300 rounded-md outline-none bg-opacity-10 focus:border-gray-500 focus:ring-transparent focus:outline-none"
                    />
                    <button type="submit" class="p-2 ml-2 text-gray-400 bg-gray-100 rounded-full hover:text-gray-600 hover:bg-gray-200 focus:outline-none" aria-label="Upload a files">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-6 rotate-90" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z" />
                        </svg>
                    </button>
                </div>
            </div>

        </form>

        <div v-if="$page.props.user.current_team.is_deleted" class="flex items-center justify-center py-2">
            <span class="font-semibold text-blue-400">{{ checkWhoLeftTheConversation().name }}</span> &nbsp; has left this conversation.
        </div>

        <form  v-if="!$page.props.user.current_team.is_deleted && !loadingMiddleSection && $page.props.user.current_team && $page.props.user.current_team.team_type === 'private-message'"
               @submit.prevent="writeMessagePrivate"
               class="flex flex-col relative items-center p-3 border-t-2 border-gray-200"
        >
            <gif-search
                v-if="showGifSearch"
                :showGifSearch="showGifSearch"
                :closeGifSearch="closeGifSearch"
                :setMessagePrivate="setMessagePrivate"
                :writeMessagePrivate="writeMessagePrivate"
            />

            <div v-if="privateOpenedFiles && privateOpenedFiles.length > 0" class="w-full px-16 pb-5">
                <div class="flex flex-wrap w-full">
                    <div v-for="(file, i) in privateOpenedFiles" :key="i" class="flex items-end p-1 m-1 bg-blue-100 rounded-md shadow">
                        <div class="flex flex-col justify-center mt-3 text-xs font-semibold">
                            <div v-if="getFileExtension(file) === 'mp4'" class="bg-gray-900">
                                <video style="width: 20rem; height: 20rem;" controls>
                                    <source :src="getImgPath(file)" />
                                </video>
                            </div>
                            <img v-if="getFileExtension(file) === 'pdf'" src="/pdf.png" alt="pdf" class="w-8 h-8 mr-2" />
                            <img v-if="getFileExtension(file) === 'xlsx'" src="/excel.png" alt="Excell" class="w-8 h-8 mr-2" />
                            <img v-if="getFileExtension(file) === 'doc'" src="/word.png" alt="Word" class="w-8 h-8 mr-2" />

                            <img
                                v-if="getFileExtension(file) === 'jfif' || getFileExtension(file) === 'jpg' || getFileExtension(file) === 'jpeg' || getFileExtension(file) === 'png'"
                                :src="getImgPath(file)"
                                alt="No Image :("
                                class="w-24 h-24 mr-2 rounded-md shadow"
                            >
                            <img v-if="getFileExtension(file) === 'rtf' || getFileExtension(file) === 'txt'" src="/doc.png" alt="Document" class="w-8 h-8 mr-2" />
                            <span class="text-xs text-gray-600">{{ file.name }}</span>
                        </div>
                        <button @click="removePrivateConversationFile(i)" type="button" class="p-1.5 ml-2 text-gray-400 rounded-full hover:text-gray-600 bg-gray-300 focus:outline-none">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </div>
                </div>
            </div>
            <div class="flex items-center w-full">
                <div class="">
                    <button @click="onPickFilePrivate" type="button" class="p-2 text-gray-400 rounded-full hover:text-gray-600 hover:bg-gray-100 focus:outline-none" aria-label="Upload a files">
                        <svg class="w-6 h-6 fill-current" viewBox="0 0 20 20">
                            <path d="M4.317,16.411c-1.423-1.423-1.423-3.737,0-5.16l8.075-7.984c0.994-0.996,2.613-0.996,3.611,0.001C17,4.264,17,5.884,16.004,6.88l-8.075,7.984c-0.568,0.568-1.493,0.569-2.063-0.001c-0.569-0.569-0.569-1.495,0-2.064L9.93,8.828c0.145-0.141,0.376-0.139,0.517,0.005c0.141,0.144,0.139,0.375-0.006,0.516l-4.062,3.968c-0.282,0.282-0.282,0.745,0.003,1.03c0.285,0.284,0.747,0.284,1.032,0l8.074-7.985c0.711-0.71,0.711-1.868-0.002-2.579c-0.711-0.712-1.867-0.712-2.58,0l-8.074,7.984c-1.137,1.137-1.137,2.988,0.001,4.127c1.14,1.14,2.989,1.14,4.129,0l6.989-6.896c0.143-0.142,0.375-0.14,0.516,0.003c0.143,0.143,0.141,0.374-0.002,0.516l-6.988,6.895C8.054,17.836,5.743,17.836,4.317,16.411"></path>
                        </svg>
                    </button>
                    <input
                        v-on:change="privateFile"
                        type="file"
                        style="display: none"
                        multiple
                        ref="fileInputPrivate"
                        @change="onFilePickedPrivate"/>
                </div>
                <div class="flex items-center flex-1 px-3">
                    <button v-if="!showEmojisPrivate" @click="openEmojisPrivate" type="button" class="p-2 text-gray-400 rounded-full hover:text-gray-600 hover:bg-gray-100 focus:outline-none" aria-label="Upload a files">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </button>
                    <button v-if="showEmojisPrivate" @click="closeEmojisPrivate" type="button" class="p-2 text-gray-400 rounded-full hover:text-gray-600 hover:bg-gray-100 focus:outline-none" aria-label="Upload a files">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-6" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <img @click="openGifSearch" src="/gificon.png" class="w-8 h-8 cursor-pointer" alt="...">
                    <input
                        ref="messageInputPrivate"
                        v-model="privateMessage"
                        type="text"
                        placeholder="Write your message ..."
                        class="w-full py-2 pl-2 ml-2 text-sm text-gray-700 placeholder-gray-500 bg-gray-600 border-gray-300 rounded-md outline-none bg-opacity-10 focus:border-gray-500 focus:ring-transparent focus:outline-none"
                    />
                    <button type="submit" class="p-2 ml-2 text-gray-400 bg-gray-100 rounded-full hover:text-gray-600 hover:bg-gray-200 focus:outline-none" aria-label="Upload a files">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 w-8 rotate-90" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z" />
                        </svg>
                    </button>
                    <VuemojiPicker v-if="showEmojisPrivate" class="absolute light emojiPosition " @emojiClick="handleEmojiClick" />
                </div>
            </div>

        </form>

        <div class="w-full h-full flex flex-col items-center justify-center"
             v-if="currentMiddleSection === 'topics' && !currentTopic && $page.props.user.current_team.name === 'not-showing' && !loadingTopicConversations && !loadingPrivateMessages && !loadingMiddleSection">
            <h3 class="text-2xl text-gray-500 text-center font-bold">Forum Secretari</h3>
            <img src="/emptyappill.svg" alt="..." style="opacity: .6;">
        </div>

    </div>
</template>

<script>

import Messages from "@/Components/Messages";
import TopicConversations from "@/Components/TopicConversations";
import PrivateConversations from '@/Components/PrivateConversations';
import {VuemojiPicker} from 'vuemoji-picker';
import LoadingSpinner from "@/Components/LoadingSpinner";
import JetDialogModal from '@/Jetstream/DialogModal';
import TopicPermissionUser from "@/Components/TopicPermissionUser";
import GifSearch from "@/Components/GifSearch";
import moment from 'moment';

export default {
    name: "MiddleSection",
    components: {
        Messages,
        TopicConversations,
        PrivateConversations,
        VuemojiPicker,
        LoadingSpinner,
        JetDialogModal,
        TopicPermissionUser,
        GifSearch
    },
    props: {
        changeCurrentLeftSide: Function,
        currentTopics: Array,
        middleSection: String,
        currentTopicConversations: Array,
        topicName: String,
        privateMessages: Array,
        setMyNewMessages: Function,
        getLastNewPrivateMessage: Function,
        getLastNewTopicConversation: Function,
        canReadMessages: Boolean,
        setCanReadMessages: Function,
        setReadedTeams: Function,
        setNewGroups: Function,
        currentTopic: Object,
        switchToTeam: Function,
        setMappedGroupFiles: Function,
        setMappedGroupMedia: Function,
        gotNewInvitation: Function,
        setTotalUnreadPrivateMessages:Function,
        setTotalUnreadTopicConversations: Function,
        setCurrentMessageSeenBy: Function,
        currentMessageSeenBy: Object,
        totalUnreadPrivateMessages: Number,
        totalUnreadTopicConversations: Number,
        changeLeftCurrentComponentOnNotificationClick: Function,
        loadingMiddleSection: Boolean,
        setLoadingMiddleSection: Function,
        unreadNotificationsCount: Number,
        setUnreadNotificationsCount: Function,
        setNewNotifications: Function,
        currentLeftSide: String,
        setCurrentTeamInvitations: Function,
        setInvitationsAfterAccept: Function,
        setContactsCountAfterAccept: Function,
        openInvitationDeletedModal: Function,
    },
    data() {
        return {
            stateCurrentTopic: null,
            showEmojisPrivate: false,
            showEmojisTopic: false,
            showAddTopicForm: false,
            currentMiddleSection: 'topics',
            message: '',
            mappedPrivateMessages: [],
            mappedCurrentTopicConversations: [],
            mappedCurrentTopics: [],
            privateOpenedFiles: [],
            topicOpenedFiles: [],
            topicForm: this.$inertia.form({
                title: "",
                description: null,
                team_id: null,
                update: false,
                topic_id: null,
                permission: 'Permission',
                userPermissions: []
            }),
            topicConversationMessage: "",
            topicConversationFile: null,
            privateMessage: "",
            privateFile: null,
            socketChannel: null,
            socketChannelOnline: null,
            currentPrivateMessagesPage: 1,
            currentTopicConversationsPage: 1,
            loadingPrivateMessages: false,
            loadingTopicConversations: false,
            lazyLoadingTopicConversations: 'no-loading',
            lazyLoadingPrivateMessages: "no-loading",
            sendingPrivateMessage: false,
            sendingTopicConversation: false,
            newNotifications: [],
            currentTopicsPage: 1,
            loadingTopicsPaginate: false,
            notificationSound: new Audio('/get-audio/new-message.wav'),
            showGifSearch: false,
            showGifSearchTopic: false,
        }
    },
    methods: {
        openGifSearch() {
            this.showGifSearch = true;
        },
        closeGifSearch() {
            this.showGifSearch = false;
        },
        openGifSearchTopic() {
            this.showGifSearchTopic = true;
        },
        closeGifSearchTopic() {
            this.showGifSearchTopic = false;
        },
        userIsTyping() {
            console.log('called')
            window.userChannel.whisper('typing', { test: 'test' });
        },
        topicConversationScroll() {
            this.$refs.topicConversationsRef.scrollingToBottom();
        },
        checkWhoLeftTheConversation() {
            if (this.$page.props.user.current_team.owner.id === this.$page.props.user.id) {
                return this.$page.props.user.current_team.receiver;
            }
            if (this.$page.props.user.current_team.receiver.id === this.$page.props.user.id) {
                return this.$page.props.user.current_team.owner;
            }
        },
        removeNotification(index) {
            this.newNotifications.splice(index, 1, 0);
        },
        getUserPhoto(user) {
            if (user.profile_photo_path) {
                return "/get-profile-photos/"+user.profile_photo_path;
            } else {
                return '/user.png'
            }
        },
        onTopicNotificationClick(newNotification) {
            this.switchToTeam(newNotification.team.id)
        },
        findUserPermissionWhenEditTopic(userId) {
            return Object.values(this.topicForm.userPermissions).find(permission => permission.userId === userId)
        },
        handleTopicPermissionUser(data) {
            const existingPermission = Object.values(this.topicForm.userPermissions).find(permission => permission.userId === data.user.id);
            if (existingPermission) {
                existingPermission.permission = data.permission;
                return;
            }
            this.topicForm.userPermissions.push({ userId: data.user.id, permission: data.permission });
        },
        playNotificationSound() {
            const soundNotificationOption = localStorage.getItem('esn');
            if (soundNotificationOption && soundNotificationOption === 'enabled') {
                this.notificationSound.play();
            }
        },
        handleEmojiClick(event) {
            this.privateMessage = this.privateMessage+event.unicode;
            this.$refs.messageInputPrivate.focus();
        },
        openEmojisPrivate() {
            this.showEmojisPrivate = true;
        },
        closeEmojisPrivate() {
            this.showEmojisPrivate = false;
        },
        handleEmojiClickTopic(event) {
            this.topicConversationMessage = this.topicConversationMessage+event.unicode;
            this.$refs.messageInput.focus();
        },
        openEmojisTopic() {
            this.showEmojisTopic = true;
        },
        closeEmojisTopic() {
            this.showEmojisTopic = false;
        },
        getImgPath(file) {
            return URL.createObjectURL(file)
        },
        getFileExtension(file) {
            return file.name.trim(" ").split('.').pop();
        },
        onPickFile () {
            this.$refs.fileInput.click()
        },
        onPickFilePrivate () {
            this.$refs.fileInputPrivate.click()
        },
        removeTopicConversationFile(index) {
            this.topicOpenedFiles.splice(index, 1);
        },
        removePrivateConversationFile(index) {
            this.privateOpenedFiles.splice(index, 1);
        },
        onFilePicked (e) {
            e.preventDefault();
            this.topicConversationFile = e.target.files[0];

            if (e.target.files.length === 1) {
                this.topicOpenedFiles.push(e.target.files[0]);
            }

            if (e.target.files.length > 1) {
                this.topicOpenedFiles = [...this.topicOpenedFiles, ...e.target.files];
            }
            this.topicConversationFile = null;
            this.$refs.messageInput.focus();
        },
        onFilePickedPrivate (e) {
            e.preventDefault();
            this.privateFile = e.target.files[0];

            if (e.target.files.length === 1) {
                this.privateOpenedFiles.push(e.target.files[0]);
            }

            if (e.target.files.length > 1) {
                this.privateOpenedFiles = [...this.topicOpenedFiles, ...e.target.files];
            }

            this.privateFile = null;
            this.$refs.messageInputPrivate.focus();
        },
        openShowAddTopicForm() {
            this.topicForm.reset();
            this.showAddTopicForm = true;
        },
        closeShowAddTopicForm() {
            this.showAddTopicForm = false;
            this.topicForm.permission = "Permission";
            this.topicForm.userPermissions = [];
        },
        changeCurrentMiddleSection(component) {
            this.currentMiddleSection = component;
        },
        setCurrentTopic(topicName) {
            this.currentTopic = topicName;
        },
        postTopic(teamId) {
            this.topicForm.team_id = teamId;
            if (this.topicForm.permission !== 'Permission') {
                if (this.topicForm.permission === 'custom') {
                    const teamUsers = [...this.$page.props.user.current_team.users, this.$page.props.user.current_team.owner];
                    if (teamUsers.length -1 !== this.topicForm.userPermissions.length) {
                        return;
                    }
                }
                this.topicForm.post(route('topic.store'
                ), {
                    preserveState: true,
                    preserveScroll: true,
                    onSuccess: (response) => {
                        this.topicForm.reset();
                        this.topicForm.permission = "Permission";
                        this.showAddTopicForm = false;
                        this.mappedCurrentTopics = response.props.currentTopics;
                    }
                })
            }
        },
        writeMessage() {
            if (!this.topicConversationMessage && this.topicOpenedFiles.length === 0) {
                return;
            } else {
                this.setNewTempMessageTopic(this.currentTopic.id, this.topicName);
            }
        },
        writeMessagePrivate() {
            if (!this.privateMessage && this.privateOpenedFiles.length === 0) {
                return;
            } else {
                this.setNewTempMessage();
            }
        },
        setMessagePrivate(message) {
              this.privateMessage = message;
        },
        setMessageTopic(message) {
            this.topicConversationMessage = message;
        },
        setNewTempMessage() {
            this.sendingPrivateMessage = true;
            let formdata = new FormData();
            Object.values(this.privateOpenedFiles).forEach(file => {
                formdata.append('files[]', file);
            });
            formdata.append('message', this.privateMessage);
            this.privateMessage = "";

            window.axios.post(`/private-conversation?tempUid`,
                formdata, { headers: {'Content-Type': 'multipart/form-data'}
                }).then(res => {
                this.privateFile = null;
                this.closeEmojisPrivate();
                this.privateOpenedFiles = [];
                if (res.data.newPrivateMessage && !res.data.error) {
                    this.mappedPrivateMessages.push(res.data.newPrivateMessage);
                    this.$refs.privateConversationsRef.scrollingToBottom();
                    this.setMyNewMessages(res.data.newPrivateMessage);
                    this.setMappedGroupFiles(res.data.groupFiles);
                    this.setMappedGroupMedia(res.data.groupMedia);
                } else {
                    this.openInvitationDeletedModal(null, `${this.checkWhoLeftTheConversation().name} was left the conversation.`);
                }
                this.sendingPrivateMessage = false;
            });
        },
        setNewTempMessageTopic(topicId, topicName) {
            this.sendingTopicConversation = true;
            let formdata = new FormData();
            Object.values(this.topicOpenedFiles).forEach(file => {
                formdata.append('files[]', file);
            });
            formdata.append('message', this.topicConversationMessage);
            this.topicConversationMessage = "";

            window.axios.post(`/topic-conversation?topicId=${topicId}&topicName=${topicName}`,
                formdata,
                { headers: { 'Content-Type': 'multipart/form-data' } }
            ).then(res => {
                this.topicConversationFile = null;
                this.closeEmojisTopic();
                this.topicOpenedFiles = [];
                this.mappedCurrentTopicConversations.push(res.data.newTopicConversation);
                this.$refs.topicConversationsRef.scrollingToBottom();
                this.setMyNewMessages(res.data.newTopicConversation);
                this.setMappedGroupFiles(res.data.groupFiles);
                this.setMappedGroupMedia(res.data.groupMedia);
                this.sendingTopicConversation = false;
            });
        },
        getNewMessage(message) {
            this.mappedPrivateMessages.push(message);
            this.getLastNewPrivateMessage(message);
        },
        getNewTopicConversation(message) {
            this.mappedCurrentTopicConversations.push(message);
            this.getLastNewTopicConversation(message);
        },
        editTopic(topic) {
            this.topicForm.title = topic.title;
            this.topicForm.team_id = topic.team_id;
            this.topicForm.topic_id = topic.id;
            this.topicForm.update = true;
            this.topicForm.description = topic.description;
            this.showAddTopicForm = true;
            this.topicForm.permission = topic.permission;
            if (topic.permission === 'custom') {
                topic.user_permissions.forEach(permission => {
                    this.topicForm.userPermissions.push({
                        userId: permission.user_id,
                        permission: permission.permission
                    });
                });
            }
        },
        editingTopic() {
            if (this.topicForm.update) {
                this.topicForm.put(route('topic.update'), {
                    onSuccess: (response) => {
                        this.topicForm.reset();
                        this.showAddTopicForm = false;
                        this.mappedCurrentTopics = response.props.currentTopics;
                        this.topicForm.permission = "Permission"
                    }
                });
            }
        },
        setMappedCurrentPrivateMessages(messages) {
              this.mappedPrivateMessages = messages
        },
        setMappedCurrentTopics(topics) {
            this.mappedCurrentTopics = topics;
        },
        listenForSocketChanges() {

            if (!window.userChannel || !window.onlineUsersChannel) {
                window.userChannel = Echo.private(`user-${this.$page.props.user.id}`);
                window.onlineUsersChannel = Echo.join('online-users');
            }

            window.userChannel.subscribe();


            window.userChannel.listen('.new-message', (data) => {
                if (data.type === 'private') {
                    window.axios.get(`/get-new-private-messages?pmid=${data.message.id}`).then(res => {
                        if (this.$page.props.user.current_team_id === res.data.message.team_id) {
                            this.getNewMessage(res.data.message);
                            if (document.visibilityState === 'hidden') {
                                this.notifyUser({
                                    type: 'private-message',
                                    message: data.message
                                });
                            }
                        } else {
                            if (document.visibilityState === 'hidden') {
                                this.notifyUser({
                                    type: 'private-message',
                                    message: data.message
                                });
                            }
                        }
                        this.setMappedGroupFiles(res.data.groupFiles);
                        this.setMappedGroupMedia(res.data.groupMedia);
                        this.playNotificationSound();
                        this.setNewGroups(res.data.teams);
                        this.setCanReadMessages(true);
                        this.setTotalUnreadPrivateMessages(res.data.totalUnreadPrivateMessages);
                        this.setTotalUnreadTopicConversations(res.data.totalUnreadTopicConversations);
                        this.$refs.privateConversationsRef.scrollingToBottom();
                    });
                }
                if (data.type === 'group') {
                    window.axios.get(`/get-new-private-messages?tcid=${data.message.id}&tid=${data.topicId}`).then(res => {
                        if (this.$page.props.user.current_team_id === res.data.message.team_id && this.currentTopic && this.currentTopic.id === res.data.message.topic_id) {
                            this.$refs.topicConversationsRef.scrollingToBottom();
                            if (document.visibilityState === 'hidden') {
                                this.notifyUser({
                                    type: "group",
                                    message: data.message
                                });
                            }
                        } else {
                            if (document.visibilityState === 'hidden') {
                                this.notifyUser({
                                    type: "group",
                                    message: data.message
                                });
                            }
                        }

                        this.setMappedGroupFiles(res.data.groupFiles);
                        this.setMappedGroupMedia(res.data.groupMedia);
                        if (this.$page.props.user.current_team_id === res.data.message.team_id && this.middleSection === 'topics') {
                            this.mappedCurrentTopics = res.data.currentTopics;
                        }
                        this.playNotificationSound();
                        this.setNewGroups(res.data.teams);
                        this.setCanReadMessages(true);
                        this.setTotalUnreadPrivateMessages(res.data.totalUnreadPrivateMessages);
                        this.setTotalUnreadTopicConversations(res.data.totalUnreadTopicConversations);
                        this.getNewTopicConversation(res.data.message);
                    });
                }
            });

            window.userChannel.listen('.new-topic-created', (data) => {
                if (data.userId === this.$page.props.user.id && data.teamId === this.$page.props.user.current_team_id) {
                    window.axios.get(`/get-created-topic?topicId=${data.topicId}&teamId=${data.teamId}`).then(res => {
                        this.mappedCurrentTopics = [res.data.topic, ...this.mappedCurrentTopics];
                        if (document.visibilityState === 'hidden') {
                            this.notifyNewTopic(res.data.topic.title, res.data.team);
                            return;
                        }
                    })
                }
            });

            // Get new invitation
            window.userChannel.listen('.new-invitation', (data) => {
                if (data && data.invitation) {
                    this.gotNewInvitation(data.invitation);
                    if (document.visibilityState === 'hidden') {
                        this.notifyInvitation(data);
                    }
                }
            });

            // Accepted new invitation
            window.userChannel.listen('.invite-was-accepted-event', (data) => {
                if (data.teamType === 'private-message') {
                    window.axios.get('/get-groups').then(res => {
                        this.setNewGroups(res.data.teams);
                        this.setInvitationsAfterAccept(res.data.invitationSent);
                        this.setContactsCountAfterAccept(res.data.teams);
                    })
                }
                if (data.teamType === 'group') {
                    window.axios.get('/get-current-group').then(res => {
                        this.$page.props.user.current_team = res.data;
                        this.setCurrentTeamInvitations(res.data.team_invitations);
                    })
                }
            });

            const updateMyTeams = (user) => {
                Object.values(this.$page.props.all_teams).find(team => {
                    if (team.team_type === 'private-message') {
                        if (team.owner.id === user.id) {
                            team.owner = user;
                        }
                        if (team.receiver.id === user.id) {
                            team.receiver = user;
                        }
                    }
                });

                if (this.$page.props.user.current_team.team_type === 'group') {
                    if (this.$page.props.user.current_team.owner.id === user.id) {
                        this.$page.props.user.current_team.owner.status = user.status;
                        return;
                    }

                    Object.values(this.$page.props.user.current_team.users).forEach(groupUser => {
                        if (groupUser.id === user.id) {
                            groupUser.status = user.status;
                        }
                    })
                }

                return;
            }


            window.onlineUsersChannel.joining( (user) => {
                window.axios.post('/change-user-status-online', { userId: user.id }).then(res => res.data);

            }).leaving( (user) => {
                window.axios.post('/change-user-status-offline', { userId: user.id }).then(res => res.data);


            }).listen('.user-online-event', (data) => {
                if (data.user.id === this.$page.props.user.current_team.owner.id) {
                    this.$page.props.user.current_team.owner.status = 'online';
                    updateMyTeams(this.$page.props.user.current_team.owner);
                    return;
                }
                if (this.$page.props.user.current_team.receiver && data.user.id === this.$page.props.user.current_team.receiver.id) {
                    this.$page.props.user.current_team.receiver.status = 'online';
                    updateMyTeams(this.$page.props.user.current_team.receiver);
                    return;
                }
                data.user.status = 'online';
                updateMyTeams(data.user);
            }).listen('.user-offline-event', (data) => {
                if (data.user.id === this.$page.props.user.current_team.owner.id) {
                    this.$page.props.user.current_team.owner.status = 'offline';
                    updateMyTeams(this.$page.props.user.current_team.owner);
                    return;
                }
                if (this.$page.props.user.current_team.receiver && data.user.id === this.$page.props.user.current_team.receiver.id) {
                    this.$page.props.user.current_team.receiver.status = 'offline';
                    updateMyTeams(this.$page.props.user.current_team.receiver);
                    return;
                }
                data.user.status = 'offline';
                updateMyTeams(data.user);
            });
        },
        notifyInvitation(data) {
            if (Notification.permission !== "granted") {
                Notification.requestPermission();
            } else {
                const browserNotificationOption = localStorage.getItem('ebn');
                if (browserNotificationOption && browserNotificationOption === 'enabled') {
                    const notificationTitle = () => {
                        if (data.invitation.team.team_type === 'private-message') {
                            return data.invitation.team.owner.name;
                        }
                        if (data.invitation.team.team_type === 'group') {
                            return data.invitation.team.name;
                        }
                    }
                    const notificationIcon = () => {
                        if (data.invitation.team.team_type === 'private-message') {
                            return data.invitation.team.owner.profile_photo_path ?
                                `/get-profile-photos/${data.invitation.team.owner.profile_photo_path}` :
                                "/avatar.png"
                        }
                        if (data.invitation.team.team_type === 'group') {
                            return data.invitation.team.profile_photo_path ?
                                `/get-profile-photos/${data.invitation.team.profile_photo_path}` :
                                "/groupavatar.png"
                        }
                    }

                    const notification = new Notification(notificationTitle(), {
                        body: 'New Invitation',
                        icon: notificationIcon()
                    });
                } else {
                    return;
                }
            }
        },
        notifyUser(data) {
            if (Notification.permission !== "granted") {
                Notification.requestPermission();
            } else {
                const browserNotificationOption = localStorage.getItem('ebn');
                if (browserNotificationOption && browserNotificationOption === 'enabled') {
                    const notificationImg = () => {
                        if (data.type === 'private-message') {
                            return data.message.created_by.profile_photo_path ?
                                `/get-profile-photos/${data.message.created_by.profile_photo_path}` :
                                "/avatar.png"
                        }
                        if (data.type === 'group') {
                            return data.message.team.profile_photo_path ?
                                `/get-profile-photos/${data.message.team.profile_photo_path}` :
                                "/groupavatar.png"
                        }
                    }
                    const notificationTitle = () => {
                        if (data.type === 'private-message') {
                            return data.message.created_by.name
                        }
                        if (data.type === 'group') {
                            return data.message.team.name
                        }
                    }
                    const notificationBody = () => {
                        if (data.message.files && data.message.files.length > 0 && !data.message.message) {
                            return data.message.files.pop().original_filename;
                        } else {
                            return data.message.message
                        }
                    }
                    const notification = new Notification(notificationTitle(), {
                        body: notificationBody(),
                        icon: notificationImg(),
                        vibrate: true
                    });
                    notification.onclick = (event) => {
                        event.preventDefault();
                        if (data.type === 'group') {
                            this.changeLeftCurrentComponentOnNotificationClick('group');
                        }
                        if (data.type === 'private-message') {
                            this.changeLeftCurrentComponentOnNotificationClick('contacts');
                        }
                        this.switchToTeam(data.message.team_id)
                        window.focus();
                    }
                } else {
                    return;
                }
            }
        },
        notifyNewTopic(topicTitle, team) {
            if (Notification.permission !== "granted") {
                Notification.requestPermission();
            } else {
                const browserNotificationOption = localStorage.getItem('ebn');
                if (browserNotificationOption && browserNotificationOption === 'enabled') {
                    const notificationIcon = () => {
                        return team.profile_photo_path ?
                            `/get-profile-photos/${team.profile_photo_path}` :
                            "/groupavatar.png"
                    }

                    const notification = new Notification(`New Topic in ${team.name}`, {
                        body: topicTitle,
                        icon: notificationIcon()
                    });
                } else {
                    return;
                }
            }
        },
        setLazyLoadingPrivateMessages(status) {
            this.lazyLoadingPrivateMessages = status;
        },
        detectScrollToTop() {
            if (this.$page.props.user.current_team.team_type === 'private-message') {
                const privateList = document.getElementById('privateScrollingArea');
                privateList.addEventListener('scroll', async (event) => {
                    if (privateList.scrollTop === 0) {
                        this.lazyLoadingPrivateMessages = 'loading';
                        this.loadingPrivateMessages = true;
                        this.currentPrivateMessagesPage++;
                        await window.axios.get(`/get-private-messages-paginate?page=${this.currentPrivateMessagesPage}`).then(res => {
                            if (res.data.length > 0) {
                                this.mappedPrivateMessages = [...res.data.reverse(), ...this.mappedPrivateMessages];
                                this.loadingPrivateMessages = false;
                                this.lazyLoadingPrivateMessages = 'loaded';
                                if (res.data.length === 15) {
                                    privateList.scrollTop = 1500;
                                } else {
                                    privateList.scrollTop = 50;
                                }
                                return;
                            }
                            this.lazyLoadingPrivateMessages = 'loaded';
                        });
                    }
                });
            }
        },
        detectScrollToTopTopic() {
            if (this.$page.props.user.current_team.team_type === 'group') {
                const topicConversationList = document.getElementById('topicScrollingArea');
                topicConversationList.addEventListener('scroll', async (event) => {
                    if (topicConversationList.scrollTop === 0) {
                        this.lazyLoadingTopicConversations = 'loading';
                        this.loadingTopicConversations = true;
                        this.currentTopicConversationsPage++;
                        await window.axios.get(`/get-topic-conversations-paginate?page=${this.currentTopicConversationsPage}&topicId=${this.currentTopic.id}`).then(res => {
                            if (res.data.length > 0) {
                                this.mappedCurrentTopicConversations = [...res.data.reverse(), ...this.mappedCurrentTopicConversations];
                                this.loadingTopicConversations = false;
                                this.lazyLoadingTopicConversations = 'loaded';
                                if (res.data.length === 15) {
                                    topicConversationList.scrollTop = 150;
                                } else {
                                    topicConversationList.scrollTop = 50;
                                }
                                return;
                            }
                            this.lazyLoadingTopicConversations = 'loaded';
                        });
                    }
                });
            }
        },
        detectTopicsScrollingToBottom() {
            const topicList = document.getElementById('topicsScrollingArea');
            if (topicList) {
                topicList.addEventListener('scroll', (event) => {
                    if( topicList.scrollTop === (topicList.scrollHeight - topicList.offsetHeight)) {
                        this.loadingTopicsPaginate = true;
                        this.currentTopicsPage++;
                        window.axios.get(`/topics-paginate?page=${this.currentTopicsPage}`).then(res => {
                            if (res.data.length > 0) {
                                this.mappedCurrentTopics = [...this.mappedCurrentTopics, ...res.data];
                            }
                            this.loadingTopicsPaginate = false;
                        })
                    }
                })
            }
        }
    },
    mounted() {
        this.stateCurrentTopic = this.currentTopic;
        this.currentPrivateMessagesPage = 1;
        this.currentTopicConversationsPage = 1;
        this.currentMiddleSection = this.middleSection;
        this.mappedPrivateMessages = this.privateMessages.reverse();
        this.mappedCurrentTopicConversations = this.currentTopicConversations.reverse();
        this.mappedCurrentTopics = this.currentTopics;
        this.listenForSocketChanges();
        this.detectScrollToTop();
        this.detectScrollToTopTopic();
        this.detectTopicsScrollingToBottom();
        if (Notification.permission !== "granted") {
            Notification.requestPermission();
        }
    },
    unmounted() {
        window.userChannel.unsubscribe();
        // Echo.disconnect();
    },
    updated() {
        if (this.lazyLoadingPrivateMessages !== 'loaded' && this.lazyLoadingPrivateMessages !== 'loading' && !this.currentMessageSeenBy) {
            if (this.$refs.privateConversationsRef) {
                this.$refs.privateConversationsRef.scrollingToBottom();
            }
        }
        if (this.lazyLoadingTopicConversations !== 'loaded' && this.lazyLoadingTopicConversations !== 'loading' && !this.currentMessageSeenBy) {
            if (this.$refs.topicConversationsRef) {
                this.$refs.topicConversationsRef.scrollingToBottom();
            }
        }
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
    .notification-box {
        width: 20rem;
        z-index: 80;
    }
    .emojiPosition {
        left: 0;
        bottom: 72px;
    }
    .spinnerPosition {
        top: 6rem;
        z-index: 60;
    }

</style>
