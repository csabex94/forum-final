<template>
    <div class="flex flex-col items-stretch justify-start w-full bg-white rounded-md lg:rounded-none lg:rounded-l-md">
        <div class="flex flex-col flex-auto">
            <div class="flex flex-row flex-auto">
                <div class="flex flex-col w-full">
                    <div class="flex items-center w-full p-3">
                        <input
                            v-model="searchGroupQuery"
                            type="text"
                            placeholder="Search"
                            class="w-full py-1 pl-10 mr-2 text-sm text-gray-400 placeholder-gray-500 bg-gray-600 border-gray-300 rounded-md outline-none search-input bg-opacity-10 focus:border-gray-500 focus:ring-transparent focus:outline-none"
                        />
                        <jet-dropdown>
                            <template #trigger>
                                <button class="flex has-tooltip relative flex-col items-center justify-center p-2 rounded-full hover:bg-gray-200 hover:bg-opacity-30 focus:outline-none" aria-label="Add">
                                    <svg class="w-4 h-4 fill-current" viewBox="0 0 25 25">
                                        <path d="M11 11v-11h1v11h11v1h-11v11h-1v-11h-11v-1h11z"/>
                                    </svg>
                                    <span class='tooltip left-2 -bottom-8 w-48 z-50 text-xs rounded-lg shadow-lg  p-2 text-white bg-gray-500'>Create group or add contact
                                   <svg class="z-40 w-4 h-4 absolute left-2 rotate-180 -top-1.5 text-gray-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M17.707 9.293a1 1 0 010 1.414l-7 7a1 1 0 01-1.414 0l-7-7A.997.997 0 012 10V5a3 3 0 013-3h5c.256 0 .512.098.707.293l7 7zM5 6a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path></svg>
                                </span>
                                </button>
                            </template>
                            <template #content>
                                <jet-dropdown-link @click="changeCurrentRightSide('add-group')" as="button">
                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-4 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z" />
                                        </svg>
                                        Add Group
                                    </div>
                                </jet-dropdown-link>
                                <jet-dropdown-link @click="changeCurrentRightSide('add-contact')" as="button">
                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-4 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6zM16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z" />
                                        </svg>
                                        Add Contact
                                    </div>
                                </jet-dropdown-link>
                            </template>
                        </jet-dropdown>
                    </div>

                        <div v-if="$page.props.all_teams.length === 0 && $page.props.privateMessages.length === 0">
                            <p class="flex flex-col items-center py-2 text-gray-500 px-auto">No chats available</p>
                        </div>

                        <ul class="flex flex-row items-center justify-between px-3 py-2 border-t-2 border-b-2 border-gray-100">
                            <li
                                class="relative px-3 py-1 text-xs font-semibold rounded-md cursor-pointer hover:bg-gray-200"
                                :class="currentComponent === 'groups' ? 'bg-gray-200' : ''"
                                @click="setCurrentComponent('groups')"
                            >
                                Groups <span v-if="stateTotalUnreadTopicConversations" class="absolute px-2 py-1 text-xs text-white bg-blue-400 rounded-full -top-3 right-1">
                                    {{stateTotalUnreadTopicConversations}}
                                </span>
                            </li>
                            <li
                                class="relative px-3 py-1 text-xs font-semibold rounded-md cursor-pointer hover:bg-gray-200"
                                :class="currentComponent === 'contacts' ? 'bg-gray-200' : ''"
                                @click="setCurrentComponent('contacts')"
                            >
                                Contacts <span v-if="stateTotalUnreadPrivateMessages" class="absolute px-2 py-1 text-xs text-white bg-blue-400 rounded-full -top-3 right-1">
                                    {{stateTotalUnreadPrivateMessages}}
                                </span>
                            </li>
                            <li
                                class="relative px-3 py-1 text-xs font-semibold rounded-md cursor-pointer hover:bg-gray-200"
                                :class="currentComponent === 'invites' ? 'bg-gray-200' : ''"
                                @click="setCurrentComponent('invites')"
                            >
                                Invites
                                <span v-if="mappedInvitationGot && mappedInvitationGot.length > 0" class="absolute px-2 py-1 text-xs text-white bg-blue-400 rounded-full -top-3 right-1">
                                    {{mappedInvitationGot.length}}
                                </span>
                            </li>
                            <li
                                class="relative px-3 py-1 text-xs font-semibold rounded-md cursor-pointer hover:bg-gray-200"
                                :class="currentComponent === 'pending' ? 'bg-gray-200' : ''"
                                @click="setCurrentComponent('pending')"
                            >
                                Pending
                                <span v-if="mappedPendingInvitations && mappedPendingInvitations.length > 0" class="absolute px-2 py-1 text-xs text-white bg-blue-400 rounded-full -top-3 right-1">
                                    {{mappedPendingInvitations.length}}
                                </span>
                            </li>
                        </ul>

                        <ul v-if="currentComponent === 'invites'" class="px-2">

                            <div v-if="mappedInvitationGot.length === 0" class="w-full h-full mt-32 flex items-center justify-center">
                                <img src="/inviteill.svg" alt="..." class="illSize" style="opacity: 0.6;">
                            </div>


                            <li v-for="invite in mappedInvitationGot" :key="invite.id">
                                <div v-if="invite.team && invite.team.team_type === 'private-message'" class="flex items-center justify-between px-1 py-2">
                                    <div class="mr-2">
                                        <img :src="invite.team.owner.profile_photo_path ? '/get-profile-photos/'+invite.team.owner.profile_photo_path : '/user.png'"
                                             class="object-cover w-12 h-12 mr-2 bg-gray-700 rounded-full"
                                             :class="!invite.team.owner.profile_photo_path && 'contrast'"
                                             alt="">
                                    </div>
                                    <div class="flex items-center justify-between w-full">
                                        <div class="flex flex-col justify-center">
                                            <h2 class="text-sm font-bold">{{ invite.team.owner.name }}</h2>
                                            <span class="text-sm text-gray-400">{{ invite.team.owner.email }}</span>
                                        </div>

                                        <div class="flex items-center justify-between">
                                            <button
                                                @click="acceptGroupInvitation(invite)"
                                                type="button"
                                                class="flex flex-col items-center justify-center w-8 h-8 p-1 text-gray-400 rounded-full hover:text-gray-600 hover:bg-green-300 focus:outline-none focus:ring"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                </svg>
                                            </button>

                                            <button
                                                @click="declineInvitation(invite)"
                                                type="button"
                                                class="flex flex-col items-center justify-center w-8 h-8 p-1 text-gray-400 rounded-full hover:text-gray-600 hover:bg-red-300 focus:outline-none focus:ring"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div v-if="invite.team && invite.team.team_type === 'group'" class="flex items-center justify-between px-1 py-2">
                                    <div class="mr-2">
                                        <img :src="invite.team.profile_photo_path ? '/get-profile-photos/'+invite.team.profile_photo_path : '/group.png'"
                                             class="object-cover w-12 h-12 mr-2 bg-gray-700 rounded-full"
                                             :class="!invite.team.profile_photo_path && 'contrast'"
                                             alt="">
                                    </div>
                                    <div class="flex items-center justify-between w-full">
                                        <div class="flex flex-col justify-center">
                                            <h2 class="text-sm font-bold">{{ invite.team.name }}</h2>
                                        </div>

                                        <div class="flex items-center justify-between">
                                            <button
                                                @click="acceptGroupInvitation(invite)"
                                                type="button"
                                                class="flex flex-col items-center justify-center w-8 h-8 p-1 text-gray-400 rounded-full hover:text-gray-600 hover:bg-green-300 focus:outline-none focus:ring"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                </svg>
                                            </button>

                                            <button
                                                @click="declineInvitation(invite)"
                                                type="button"
                                                class="flex flex-col items-center justify-center w-8 h-8 p-1 text-gray-400 rounded-full hover:text-gray-600 hover:bg-red-300 focus:outline-none focus:ring"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>

                        <ul v-if="!loadingResults && currentComponent === 'pending'">
                            <div v-if="!mappedPendingInvitations || mappedPendingInvitations.length === 0" class="w-full h-full flex mt-32 items-center justify-center">
                                <img src="/pendingill.svg" alt="..." class="illSize" style="opacity: 0.6;">
                            </div>

                            <li v-for="invite in mappedPendingInvitations" :key="invite.id">
                                <div class="flex items-center justify-between px-3 py-2">
                                    <div class="mr-2">
                                        <img :src="invite.profile_photo_path ? '/get-profile-photos/'+invite.profile_photo_path : '/user.png'"
                                             class="object-cover w-12 h-12 mr-2 bg-gray-700 rounded-full"
                                             :class="!invite.profile_photo_path && 'contrast'"
                                             alt="">
                                    </div>
                                    <div class="flex items-center justify-between w-full">
                                        <div class="flex flex-col justify-center">
                                            <h2 class="text-sm font-bold">{{ invite.name }}</h2>
                                            <span class="text-sm text-gray-400">{{ invite.email }}</span>
                                        </div>

                                        <button
                                            @click.prevent="deleteContactInvitation(invite.invitationId)"
                                            type="button"
                                            class="flex flex-col items-center justify-center w-8 h-8 p-1 text-gray-400 rounded-full hover:text-gray-600 hover:bg-red-300 focus:outline-none focus:ring"
                                        >
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                </div>
                            </li>
                        </ul>

                        <div v-if="loadingResults" class="w-full flex items-center justify-center mt-16">
                            <loading-spinner />
                        </div>

                        <ul v-if="!loadingResults && $page.props.all_teams && currentComponent === 'groups'" class="overflow-y-auto groupList">

                            <div v-if="checkTeamTypes()" class="w-full flex mt-32 items-center justify-center">
                                <img src="/groupsill.svg" alt="..." class="illSize" style="opacity: 0.6; width: 22rem">
                            </div>

                            <li
                                @click.prevent="switchToTeam(team.id)"
                                v-for="(team, i) in mappedTeams"
                                :key="i"
                                class="mx-2"
                            >
                               <div
                                   v-if="team && team.name !== 'not-showing' && currentComponent === 'groups' && team.team_type === 'group'"
                                   class="flex flex-row p-2 my-2 rounded-lg cursor-pointer hover:shadow hover:bg-gray-200"
                                   :class="$page.props.user.current_team_id === team.id ? 'bg-gray-200 shadow' : ''"
                               >
                                   <div class="mr-5" v-if="team.team_type === 'group'">
                                       <img
                                           :src="team.profile_photo_path ? '/get-profile-photos/'+team.profile_photo_path : '/group.png'"
                                           class="object-cover w-12 h-12 mr-4 bg-gray-700 rounded-full"
                                           alt="Group Img"
                                           :class="!team.profile_photo_path && 'contrast'"
                                       >
                                   </div>

                                   <div v-if="team.team_type === 'group'" class="flex flex-col justify-center w-full">
                                       <div class="flex flex-row items-center justify-between">
                                           <h2 class="text-xs font-bold" >{{ team['name']}}</h2>

                                           <div class="flex flex-row text-xs">

<!--                                               <svg class="w-4 h-4 mr-1 text-blue-600 fill-current" viewBox="0 0 19 14">-->
<!--                                                   <path fill-rule="nonzero" d="M4.96833846,10.0490996 L11.5108251,2.571972 C11.7472185,2.30180819 12.1578642,2.27443181 12.428028,2.51082515 C12.6711754,2.72357915 12.717665,3.07747757 12.5522007,3.34307913 L12.4891749,3.428028 L5.48917485,11.428028 C5.2663359,11.6827011 4.89144111,11.7199091 4.62486888,11.5309823 L4.54038059,11.4596194 L1.54038059,8.45961941 C1.2865398,8.20577862 1.2865398,7.79422138 1.54038059,7.54038059 C1.7688373,7.31192388 2.12504434,7.28907821 2.37905111,7.47184358 L2.45961941,7.54038059 L4.96833846,10.0490996 L11.5108251,2.571972 L4.96833846,10.0490996 Z M9.96833846,10.0490996 L16.5108251,2.571972 C16.7472185,2.30180819 17.1578642,2.27443181 17.428028,2.51082515 C17.6711754,2.72357915 17.717665,3.07747757 17.5522007,3.34307913 L17.4891749,3.428028 L10.4891749,11.428028 C10.2663359,11.6827011 9.89144111,11.7199091 9.62486888,11.5309823 L9.54038059,11.4596194 L8.54038059,10.4596194 C8.2865398,10.2057786 8.2865398,9.79422138 8.54038059,9.54038059 C8.7688373,9.31192388 9.12504434,9.28907821 9.37905111,9.47184358 L9.45961941,9.54038059 L9.96833846,10.0490996 L16.5108251,2.571972 L9.96833846,10.0490996 Z"></path>-->
<!--                                               </svg>-->
<!--                                               &lt;!&ndash; When the Message was read/seen &ndash;&gt;-->
<!--                                               <span class="text-gray-400">-->
<!--                                                10:45-->
<!--                                              </span>-->
                                           </div>
                                       </div>

                                       <div class="flex flex-row items-center justify-between">
                                           <p v-if="team.last_message" class="text-xs">
                                               {{ getReducedMessage(team.last_message) }}
                                           </p>
                                           <p v-else class="flex"></p>
                                           <!-- New unread messages count -->
                                           <span
                                               v-if="team.topic_conversations && team.topic_conversations.length > 0 && currentTopicId !== team.id"
                                               class="px-2 py-1 text-xs text-center text-white bg-blue-500 rounded-full"
                                           >{{ team.topic_conversations.length }}</span>
                                       </div>
                                   </div>
                               </div>
                            </li>
                        </ul>


                    <ul v-if="!loadingResults && $page.props.all_teams && currentComponent === 'contacts'" class="overflow-y-auto groupList">

                        <div v-if="contactsCount === 0" class="w-full flex mt-32 items-center justify-center">
                            <img src="/contactsill.svg" alt="..." class="illSize" style="opacity: 0.6; width: 22rem">
                        </div>

                        <li
                            @click.prevent="switchToTeam(team.id)"
                            v-for="(team, i) in mappedTeams"
                            :key="i"
                            class="mx-2"
                        >

                            <div
                                v-if="team && team.user_id_deleted !== $page.props.user.id && !team.pending && team.name !== 'not-showing' && team.team_type === 'private-message' && currentComponent === 'contacts'"
                                class="flex flex-row p-2 my-2 rounded-lg cursor-pointer hover:shadow hover:bg-gray-200"
                                :class="$page.props.user.current_team_id === team.id ? 'bg-gray-200 shadow' : ''"
                            >
                                <div class="relative mr-5" v-if="team.team_type === 'private-message' && $page.props.user.id === team.user_id && team.receiver">
                                    <img
                                         :src="team.receiver.profile_photo_path ? '/get-profile-photos/'+team.receiver.profile_photo_path : '/user.png'"
                                         class="object-cover w-12 h-12 mr-4 bg-gray-700 rounded-full" :class="!team.receiver.profile_photo_path && 'contrast'"  alt=""
                                    >
                                    <span v-if="team.receiver.status === 'online'" class="absolute bottom-0 right-0 w-4 h-4 bg-green-400 rounded-full"></span>
                                </div>
                                <div class="relative mr-5" v-if="team.team_type === 'private-message' && team.receiver && $page.props.user.id === team.receiver.id">
                                    <img
                                         :src="team.owner.profile_photo_path ? '/get-profile-photos/'+team.owner.profile_photo_path : '/user.png'"
                                         class="object-cover w-12 h-12 mr-4 bg-gray-700 rounded-full" :class="!team.owner.profile_photo_path && 'contrast'"  alt=""
                                    >
                                    <span v-if="team.owner.status === 'online'" class="absolute bottom-0 right-0 w-4 h-4 bg-green-400 rounded-full"></span>
                                </div>

                                <div v-if="team.team_type === 'private-message'" class="flex flex-col justify-center w-full">
                                    <div class="flex flex-row items-center justify-between">

                                        <h2 v-if="team.receiver && $page.props.user.id === team.user_id" class="text-xs font-bold" >{{ team.receiver.name}}</h2>
                                        <h2 v-if="team.receiver && $page.props.user.id === team.receiver.id" class="text-xs font-bold" >{{ team.owner.name}}</h2>

                                        <div class="flex flex-row text-xs">

                                            <!--                                               <svg class="w-4 h-4 mr-1 text-blue-600 fill-current" viewBox="0 0 19 14">-->
                                            <!--                                                   <path fill-rule="nonzero" d="M4.96833846,10.0490996 L11.5108251,2.571972 C11.7472185,2.30180819 12.1578642,2.27443181 12.428028,2.51082515 C12.6711754,2.72357915 12.717665,3.07747757 12.5522007,3.34307913 L12.4891749,3.428028 L5.48917485,11.428028 C5.2663359,11.6827011 4.89144111,11.7199091 4.62486888,11.5309823 L4.54038059,11.4596194 L1.54038059,8.45961941 C1.2865398,8.20577862 1.2865398,7.79422138 1.54038059,7.54038059 C1.7688373,7.31192388 2.12504434,7.28907821 2.37905111,7.47184358 L2.45961941,7.54038059 L4.96833846,10.0490996 L11.5108251,2.571972 L4.96833846,10.0490996 Z M9.96833846,10.0490996 L16.5108251,2.571972 C16.7472185,2.30180819 17.1578642,2.27443181 17.428028,2.51082515 C17.6711754,2.72357915 17.717665,3.07747757 17.5522007,3.34307913 L17.4891749,3.428028 L10.4891749,11.428028 C10.2663359,11.6827011 9.89144111,11.7199091 9.62486888,11.5309823 L9.54038059,11.4596194 L8.54038059,10.4596194 C8.2865398,10.2057786 8.2865398,9.79422138 8.54038059,9.54038059 C8.7688373,9.31192388 9.12504434,9.28907821 9.37905111,9.47184358 L9.45961941,9.54038059 L9.96833846,10.0490996 L16.5108251,2.571972 L9.96833846,10.0490996 Z"></path>-->
                                            <!--                                               </svg>-->
                                            <!-- When the Message was read/seen -->
                                            <!--                                               <span v-if="team.private_messages && team.lastMessage" class="text-gray-400">-->
                                            <!--                                                {{ getMessageSentTime(team.private_messages[team.private_messages.length - 1].created_at) }}-->
                                            <!--                                              </span>-->
                                        </div>
                                    </div>

                                    <div class="flex flex-row items-center justify-between">
                                        <!-- Ultimate Message -->
                                        <p v-if="team.last_message" class="text-xs">
                                            {{ getReducedMessage(team.last_message) }}
                                        </p>
                                        <p v-else class="flex mt-1 px-1 text-xs rounded-md bg-indigo-400 text-white">New</p>
                                        <!-- New unread messages count -->
                                        <span
                                            v-if="team.private_messages && team.private_messages.length > 0 && $page.props.user.current_team_id !== team.id"
                                            class="px-2 py-1 text-xs text-center text-white bg-blue-500 rounded-full"
                                        >{{ team.private_messages.length }}</span>
                                    </div>
                                </div>

                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import JetDropdown from '@/Jetstream/Dropdown';
import JetDropdownLink from '@/Jetstream/DropdownLink';
import moment from 'moment';
import _ from 'lodash';
import LoadingSpinner from "@/Components/LoadingSpinner";


export default {
    name: "LeftSection",
    components: {
        JetDropdown,
        JetDropdownLink,
        LoadingSpinner
    },
    props: {
        changeCurrentRightSide: Function,
        switchToTeam: Function,
        contacts: Array,
        gotNewMessage: Object,
        newGroups: Array,
        setCanReadMessages: Function,
        readedTeams: Array,
        invitationGot: Array,
        invitationSent: Array,
        newInvitation: Object,
        stateTotalUnreadPrivateMessages: Number,
        stateTotalUnreadTopicConversations: Number,
        newPendingInvitation: Object,
        lastNewPrivateMessage: Object,
        openInvitationDeletedModal: Function
    },
    data() {
        return {
            mappedTeams: [],
            mappedInvitationGot: [],
            mappedPendingInvitations: [],
            unreadMessagesIds: [],
            searchGroupQuery: "",
            currentComponent: 'groups',
            loadingResults: false,
            contactsCount: 0,

        }
    },
    methods: {
        declineInvitation(invitation) {
            window.axios.post(`/decline-invitation?invitationId=${invitation.id}`).then(res => {
                this.mappedInvitationGot = res.data;
            });
        },
        checkTeamTypes() {
            if (this.mappedTeams && this.mappedTeams.length > 0) {
                let value;
                const groups = this.mappedTeams.filter(team => {
                    if (team) {
                        return team.team_type === 'group'
                    }
                });
                value = groups.length === 1 && groups[0].name === 'not-showing';
                return value;
            }
        },
        getTeams() {
            let list = [];
            for (const [key, value] of Object.entries(this.mappedTeams)) {
                list.push(value);
            }
            this.mappedTeams = list;
        },
        getReducedMessage(message) {
            if (message.length > 30) {
                return message.substr(0, 30)+"..."
            }
            return message;
        },
        getMessageSentTime(date) {
            return moment(date).fromNow();
        },
        gotNewInvitation(invitation) {
            this.mappedInvitationGot.push(invitation);
        },
        setNewMessages(teamId, message, createdAt, files) {
            let list = [];
            for (const [key, value] of Object.entries(this.mappedTeams)) {
                if (value.id === teamId) {
                    if (files && files.length > 0 && !message) {
                        list.push({
                            ...value,
                            last_message: Object.values(files)[Object.values(files).length -1].original_filename,
                            created_at: createdAt,
                        })
                    } else {
                        list.push({
                            ...value,
                            last_message: message,
                            created_at: createdAt,
                        })
                    }
                } else {
                        list.push(value);
                    }
                }
            this.mappedTeams = list;
        },
        findGroups: _.debounce(function() {
            this.mappedTeams = Object.values(this.$page.props.all_teams).map(team => {
                if (team.team_type === 'group') {
                    if (team.name.toLowerCase().match(new RegExp(this.searchGroupQuery.toLowerCase()))) {
                        return team;
                    }
                }
                if (team.team_type === 'private-message') {
                    if (this.$page.props.user.id === team.owner.id) {
                        if (team.receiver.name.toLowerCase().match(new RegExp(this.searchGroupQuery.toLowerCase()))) {
                            return team;
                        }
                    }
                    if (this.$page.props.user.id === team.receiver.id) {
                        if (team.owner.name.toLowerCase().match(new RegExp(this.searchGroupQuery.toLowerCase()))) {
                            return team;
                        }
                    }
                }
            });
        }, 400),
        setCurrentComponent(component) {
            localStorage.setItem('cl_s', component)
            this.currentComponent = component;
        },
        acceptGroupInvitation(invite) {
            window.axios.post('/accept-group-invitation', { invitationId: invite.id }).then(res => {
                if (res.data.error) {
                    this.openInvitationDeletedModal(invite.id, "This invitation was deleted.");
                } else {
                    this.mappedTeams = [...Object.values(this.mappedTeams), res.data.newGroup]
                    this.mappedInvitationGot = res.data.invitationGot;
                    if (res.data.newGroup.team_type === 'group') {
                        this.setCurrentComponent('groups');
                    }
                    if (res.data.newGroup.team_type === 'private-message') {
                        this.setCurrentComponent('contacts');
                    }
                    this.setContactsCount([res.data.newGroup])
                }
            })
        },
        deleteContactInvitation(invitationId) {
            window.axios.post(`/delete-contact-invitation?invitationId=${invitationId}`).then(res => {
                if (res.data.invitationsSent.length > 0) {
                    this.mappedPendingInvitations = res.data.invitationsSent;
                }
                if (res.data.invitationsSent.length === 0) {
                    this.mappedPendingInvitations = [];
                }
            });
        },
        setContactsCount(teams) {
            Object.values(teams).forEach(team => {
                if (team.team_type === 'private-message') {
                    this.contactsCount++;
                }
            })
        },
        removeDeletedInvitation() {
            this.$inertia.visit(route('home'), {
                preserveState: false
            })
        }
    },
    mounted() {
        this.setContactsCount(this.$page.props.all_teams);
    },
    created() {
        const currentLeftComponent = localStorage.getItem('cl_s');
        if (currentLeftComponent) {
            this.currentComponent = currentLeftComponent;
        }
        this.mappedTeams = Object.values(this.$page.props.all_teams);
        this.mappedInvitationGot = this.invitationGot;
        this.mappedPendingInvitations = this.invitationSent;
    },
    watch: {
        gotNewMessage: {
            immediate: true,
            deep: true,
            handler(newValue, oldValue) {
                if (newValue) {
                    this.setNewMessages(newValue.team_id, newValue.message, newValue.created_at, newValue.files);
                }
            }
        },
        readedTeams: {
            immediate: true,
            deep: true,
            handler(newValue, oldValue) {
                if (newValue && newValue.length > 0) {
                    this.mappedTeams = newValue;
                }
            }
        },
        searchGroupQuery(newVal, oldVal) {
            this.findGroups();
        },
        newGroups(newVal, oldVal) {
            this.mappedTeams = newVal;
        },
        newInvitation(newVal, oldVal) {
            if (newVal) {
                this.gotNewInvitation(newVal);
            }
        },
        newPendingInvitation(newVal, oldVale) {
            if (newVal) {
                this.mappedPendingInvitations.push(newVal)
            }
        }
    },
}
</script>

<style scoped>
    .contrast {
        filter: grayscale(1) invert(1);
    }
    .groupList {
        height: 90vh;
    }
    .groupList::-webkit-scrollbar {
        width: 6px;               /* width of the entire scrollbar */
    }
    .groupList::-webkit-scrollbar-track {
        background: lightgrey;        /* color of the tracking area */
    }
    .groupList::-webkit-scrollbar-thumb {
        background-color: gray;    /* color of the scroll thumb */
        border-radius: 10px;       /* roundness of the scroll thumb */ /* creates padding around scroll thumb */
    }
</style>
