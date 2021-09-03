<template>
        <div class="h-screen overflow-x-hidden p-2 overflow-y-auto bg-white rounded-r-md customScroll">
            <header class="flex flex-row items-center justify-end">
                <button @click="changeCurrentLeftSide('')" type="button" class="p-2 ml-2 text-gray-400 rounded-full hover:text-gray-600 hover:bg-gray-300 focus:outline-none focus:ring">
                    <svg class="w-6 h-6 fill-current" viewBox="0 0 20 20">
                        <path d="M15.898,4.045c-0.271-0.272-0.713-0.272-0.986,0l-4.71,4.711L5.493,4.045c-0.272-0.272-0.714-0.272-0.986,0s-0.272,0.714,0,0.986l4.709,4.711l-4.71,4.711c-0.272,0.271-0.272,0.713,0,0.986c0.136,0.136,0.314,0.203,0.492,0.203c0.179,0,0.357-0.067,0.493-0.203l4.711-4.711l4.71,4.711c0.137,0.136,0.314,0.203,0.494,0.203c0.178,0,0.355-0.067,0.492-0.203c0.273-0.273,0.273-0.715,0-0.986l-4.711-4.711l4.711-4.711C16.172,4.759,16.172,4.317,15.898,4.045z"></path>
                    </svg>
                </button>
            </header>
            <main v-if="$page.props.user.current_team && $page.props.user.current_team.name !== 'not-showing'">
                <div class="flex flex-col items-center justify-center my-4">

                    <div class="relative">
                        <form method="POST">

                            <div v-if="$page.props.user.current_team.team_type === 'private-message'">
                                <img v-if="$page.props.user.id === $page.props.user.current_team.user_id"
                                     :src="$page.props.user.current_team.receiver.profile_photo_path ? '/get-profile-photos/'+$page.props.user.current_team.receiver.profile_photo_path : '/user.png'"
                                     class="object-cover w-32 h-32 mb-4 bg-gray-700 rounded-full" :class="!$page.props.user.current_team.receiver.profile_photo_path && 'contrast'"  alt=""
                                >
                                <img v-if="$page.props.user.id === $page.props.user.current_team.receiver.id"
                                     :src="$page.props.user.current_team.owner.profile_photo_path ? '/get-profile-photos/'+$page.props.user.current_team.owner.profile_photo_path : '/user.png'"
                                     class="object-cover w-32 h-32 mb-4 bg-gray-700 rounded-full" :class="!$page.props.user.current_team.owner.profile_photo_path && 'contrast'"  alt=""
                                >
                            </div>

                            <div v-if="$page.props.user.current_team.team_type === 'group'">
                                <img
                                     :src="$page.props.user.current_team.profile_photo_path ? '/get-profile-photos/'+$page.props.user.current_team.profile_photo_path : '/group.png'"
                                     class="w-32 h-32 mb-4 bg-gray-700 rounded-full"
                                     alt="Group Img"
                                     :class="!$page.props.user.current_team.profile_photo_path && 'contrast'"
                                >
                            </div>

                        <input type="file" class="hidden"
                                        ref="photo" @change="uploadGroupPhoto()">
                        <button @click="selectNewPhoto" v-if="$page.props.user.current_team && $page.props.user.id === $page.props.user.current_team.owner.id && $page.props.user.current_team.name !== 'not-showing' && $page.props.user.current_team.team_type === 'group'" type="button" class="p-1.5 absolute top-0 right-0 ml-2 text-gray-400 rounded-full hover:text-gray-600 bg-gray-300 focus:outline-none focus:ring">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                            </svg>
                        </button>
                        </form>
                    </div>

                    <div v-if="!editTeamName" class="relative flex items-center justify-center w-full">
                        <h2 v-if="$page.props.user.current_team && $page.props.user.current_team.team_type === 'group'" class="text-lg font-bold text-gray-700">
                            {{ $page.props.user.current_team.name }}
                        </h2>
                        <h2 v-if="$page.props.user.current_team.team_type === 'private-message'" class="text-lg font-bold text-gray-700">
                            <div v-if="$page.props.user.id === $page.props.user.current_team.user_id" class="flex flex-col items-center">
                                <span>{{ $page.props.user.current_team.receiver.name }}</span>
                                <span class="text-sm font-medium text-gray-500">{{ $page.props.user.current_team.receiver.email }}</span>
                            </div>
                            <div v-if="$page.props.user.id === $page.props.user.current_team.receiver_id" class="flex flex-col items-center">
                                <span >{{ $page.props.user.current_team.owner.name }}</span>
                                <span class="text-sm font-medium text-gray-500">{{ $page.props.user.current_team.owner.email }}</span>
                            </div>

                        </h2>
                        <button v-if="$page.props.user.current_team && $page.props.user.id === $page.props.user.current_team.owner.id && $page.props.user.current_team.team_type === 'group'" @click="editingTeamName" type="button" class="p-1 ml-2 text-gray-400 bg-gray-300 rounded-full hover:text-gray-600 focus:outline-none focus:ring">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                            </svg>
                        </button>
                    </div>

                    <div v-if="editTeamName && $page.props.user.current_team.team_type === 'group'" class="w-full">
                        <form v-if="$page.props.user.current_team && $page.props.user.id === $page.props.user.current_team.owner.id" @submit.prevent="changeTeamName"
                              class="flex flex-col items-center w-full px-3">
                            <input
                                v-model="form.name"
                                type="text"
                                class="w-full py-2 pl-2 mr-2 text-sm text-gray-900 placeholder-gray-500 bg-gray-600 border-gray-300 rounded-md outline-none bg-opacity-10 focus:border-gray-500 focus:ring-transparent focus:outline-none"
                            />
                            <div class="flex items-center justify-center">
                                <button type="submit" class="p-2 ml-2 text-gray-400 rounded-full hover:text-gray-600 hover:bg-gray-300 focus:outline-none focus:ring">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </button>
                                <button type="button" @click="doneEditingTeamName" class="flex flex-col items-center justify-center p-2 ml-2 text-gray-400 rounded-full hover:text-gray-600 focus:ring-2 hover:bg-gray-300 hover:bg-opacity-30 focus:outline-none" aria-label="Add">
                                    <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                        <path d="M15.898,4.045c-0.271-0.272-0.713-0.272-0.986,0l-4.71,4.711L5.493,4.045c-0.272-0.272-0.714-0.272-0.986,0s-0.272,0.714,0,0.986l4.709,4.711l-4.71,4.711c-0.272,0.271-0.272,0.713,0,0.986c0.136,0.136,0.314,0.203,0.492,0.203c0.179,0,0.357-0.067,0.493-0.203l4.711-4.711l4.71,4.711c0.137,0.136,0.314,0.203,0.494,0.203c0.178,0,0.355-0.067,0.492-0.203c0.273-0.273,0.273-0.715,0-0.986l-4.711-4.711l4.711-4.711C16.172,4.759,16.172,4.317,15.898,4.045z"></path>
                                    </svg>
                                </button>
                            </div>
                        </form>
                    </div>


                </div>

                <button
                    v-if="$page.props.user.id === $page.props.user.current_team.owner.id && $page.props.user.current_team.team_type === 'group'"
                    @click="openDeleteModal"
                    type="button"
                    class="flex items-center justify-center p-2 mx-auto text-gray-600 rounded-md hover:text-white hover:bg-gray-600 focus:outline-none"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    <p class="text-sm font-semibold">Delete Group</p>
                </button>

                <button
                    @click="leaveGroup"
                    v-if="$page.props.user.id !== $page.props.user.current_team.owner.id && $page.props.user.current_team.team_type === 'group'"
                    type="button"
                    class="flex items-center justify-center px-2 py-1 mx-auto mb-2 text-gray-600 rounded-md hover:text-white hover:bg-gray-600 focus:outline-none"
                >
                    <svg class="w-5 h-5 mr-2 fill-current" viewBox="0 0 24 24">
                        <g><path d="M0,0h24v24H0V0z" fill="none"/></g><g><path d="M17,8l-1.41,1.41L17.17,11H9v2h8.17l-1.58,1.58L17,16l4-4L17,8z M5,5h7V3H5C3.9,3,3,3.9,3,5v14c0,1.1,0.9,2,2,2h7v-2H5V5z"/></g>
                    </svg>
                    <p class="text-sm font-semibold">Leave Group</p>
                </button>


                <button
                    @click="leaveConversation"
                    v-if="$page.props.user.current_team.team_type === 'private-message'"
                    type="button"
                    class="flex items-center justify-center px-2 py-1 mx-auto mb-2 text-gray-600 rounded-md hover:text-white hover:bg-gray-600 focus:outline-none"
                >
                    <svg class="w-5 h-5 mr-2 fill-current" viewBox="0 0 24 24">
                        <g><path d="M0,0h24v24H0V0z" fill="none"/></g><g><path d="M17,8l-1.41,1.41L17.17,11H9v2h8.17l-1.58,1.58L17,16l4-4L17,8z M5,5h7V3H5C3.9,3,3,3.9,3,5v14c0,1.1,0.9,2,2,2h7v-2H5V5z"/></g>
                    </svg>
                    <p class="text-sm font-semibold">Leave Conversation</p>
                </button>


                <div class="my-2" v-if="$page.props.user.current_team.team_type === 'group' && $page.props.user.id === $page.props.user.current_team.owner.id">
                    <ul class="flex items-center py-2 justify-center rounded-lg">
                        <li class="mr-3">
                            <button
                                type="button"
                                class="p-2 flex items-center justify-center w-full h-full text-gray-800 rounded-md hover:text-gray-600 hover:bg-gray-200 focus:outline-none"
                                @click="changeCurrentComponent('group')"
                                :class="currentComponent === 'group' ? 'bg-gray-300' : ''"
                            >
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                <p class="text-xs font-semibold">Group</p>
                            </button>
                        </li>
                        <li class="">
                            <button
                                @click="changeCurrentComponent('invite-to-group')"
                                type="button"
                                v-if="$page.props.user.current_team.owner.id === $page.props.user.id"
                                :class="currentComponent === 'invite-to-group' ? 'bg-gray-300' : ''"
                                class="p-2 flex items-center justify-center w-full h-full text-gray-800 rounded-md hover:text-gray-600 hover:bg-gray-200 focus:outline-none"
                            >
                                <svg class="w-5 h-5 mr-2 fill-current" viewBox="0 0 24 24">
                                    <path d="M15 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0-6c1.1 0 2 .9 2 2s-.9 2-2 2-2-.9-2-2 .9-2 2-2zm0 8c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4zm-6 4c.22-.72 3.31-2 6-2 2.7 0 5.8 1.29 6 2H9zm-3-3v-3h3v-2H6V7H4v3H1v2h3v3z"/>
                                </svg>
                                <p class="text-xs font-semibold">Add</p>
                            </button>
                        </li>

                        <li class="ml-3 relative" v-if="$page.props.user.id === $page.props.user.current_team.owner.id">
                            <button
                                @click="changeCurrentComponent('pending-invites')"
                                type="button"
                                class="p-2 flex items-center justify-center w-full h-full text-gray-800 rounded-md hover:text-gray-600 hover:bg-gray-200 focus:outline-none"
                                :class="currentComponent === 'pending-invites' ? 'bg-gray-300' : ''"
                            >
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <p class="text-xs font-semibold">Pending</p>
                            </button>

                            <span v-if="mappedTeamInvitations.length > 0" class="absolute px-2 py-1 text-xs text-white bg-blue-400 rounded-full -top-3 right-1">
                                    {{ mappedTeamInvitations.length }}
                            </span>
                        </li>

                        <!-- <li>
                            <button type="button" class="flex flex-col items-center justify-center w-16 h-16 p-2 text-gray-400 rounded-full hover:text-gray-600 hover:bg-gray-300 focus:outline-none focus:ring">
                                <svg class="w-6 h-6 mb-2 fill-current" viewBox="0 0 24 24">
                                    <path fill-rule="nonzero" d="M11,20 L13,20 C13.5522847,20 14,20.4477153 14,21 C14,21.5128358 13.6139598,21.9355072 13.1166211,21.9932723 L13,22 L11,22 C10.4477153,22 10,21.5522847 10,21 C10,20.4871642 10.3860402,20.0644928 10.8833789,20.0067277 L11,20 L13,20 L11,20 Z M3.30352462,2.28241931 C3.6693482,1.92735525 4.23692991,1.908094 4.62462533,2.21893936 L4.71758069,2.30352462 L21.2175807,19.3035246 C21.6022334,19.6998335 21.5927842,20.332928 21.1964754,20.7175807 C20.8306518,21.0726447 20.2630701,21.091906 19.8753747,20.7810606 L19.7824193,20.6964754 L18.127874,18.9919007 L18,18.9999993 L4,18.9999993 C3.23933773,18.9999993 2.77101468,18.1926118 3.11084891,17.5416503 L3.16794971,17.4452998 L5,14.6972244 L5,8.9999993 C5,7.98873702 5.21529462,7.00715088 5.62359521,6.10821117 L3.28241931,3.69647538 C2.89776658,3.3001665 2.90721575,2.66707204 3.30352462,2.28241931 Z M7.00817933,8.71121787 L7,9 L7,14.6972244 C7,15.0356672 6.91413188,15.3676193 6.75167088,15.6624466 L6.66410059,15.8066248 L5.86851709,17 L16.1953186,17 L7.16961011,7.7028948 C7.08210009,8.02986218 7.02771758,8.36725335 7.00817933,8.71121787 Z M12,2 C15.7854517,2 18.8690987,5.00478338 18.995941,8.75935025 L19,9 L19,12 C19,12.5522847 18.5522847,13 18,13 C17.4871642,13 17.0644928,12.6139598 17.0067277,12.1166211 L17,12 L17,9 C17,6.23857625 14.7614237,4 12,4 C11.3902636,4 10.7970241,4.10872043 10.239851,4.31831953 C9.72293204,4.51277572 9.14624852,4.25136798 8.95179232,3.734449 C8.75733613,3.21753002 9.01874387,2.6408465 9.53566285,2.4463903 C10.3171048,2.15242503 11.1488212,2 12,2 Z"></path>
                                </svg>
                                <p class="text-xs font-semibold">Mute</p>
                            </button>
                        </li> -->
                    </ul>
                    <jet-dialog-modal :show="showDeleteModal" @close="closeDeleteModal">
                        <template #content>
                            <div class="justify-center flex-auto p-5 text-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="flex items-center w-6 h-6 mx-auto -m-1 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <svg xmlns="http://www.w3.org/2000/svg" class="flex items-center w-16 h-16 mx-auto text-red-500" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                                <h2 class="py-4 text-xl font-bold ">Are you sure?</h2>
                                    <p class="px-8 text-sm text-gray-500">Do you really want to delete your group?
                                        This process cannot be undone</p>
                            </div>
                        </template>
                        <template #footer>
                            <div class="p-3 mt-2 space-x-4 text-center md:block">
                                <button @click="closeDeleteModal" class="px-5 py-2 mb-2 text-sm font-medium tracking-wider text-gray-600 bg-white border rounded-full shadow-sm md:mb-0 hover:shadow-lg hover:bg-gray-100">
                                    Cancel
                                </button>
                                <button type="button" @click="deleteTeam" class="px-5 py-2 mb-2 text-sm font-medium tracking-wider text-white bg-red-500 border border-red-500 rounded-full shadow-sm md:mb-0 hover:shadow-lg hover:bg-red-600">Delete</button>
                            </div>
                        </template>
                    </jet-dialog-modal>
                </div>

                <div  class="pt-3 mt-2 border-gray-200 " v-if="currentComponent === 'invite-to-group' && $page.props.user.current_team.team_type === 'group'">
                    <invite-to-group
                        :setNewTeamInvitation="setNewTeamInvitation"
                        :changeCurrentComponent="changeCurrentComponent"
                    />
                </div>

                <ul v-if="currentComponent === 'pending-invites'" class="pt-3 mt-2 border-gray-200 ">

                    <h4 v-if="mappedTeamInvitations.length === 0" class="text-gray-500 font-semibold text-center">No pending invitations.</h4>
                    <div v-if="mappedTeamInvitations.length === 0" class="w-full flex items-center justify-center my-6">
                        <img src="/grouppendingill.svg" alt="..." style="opacity: .6; width: 18rem">
                    </div>

                    <li v-for="invite in mappedTeamInvitations" :key="invite.id" class="mb-3 bg-gray-200 rounded-md shadow px-3 py-2">
                        <div class="flex items-center justify-between w-full">
                            <div class="flex items-center">
                                <div class="mr-2">
                                    <img
                                        class="w-10 h-10 rounded-full object-cover"
                                        :src="invite.userPhoto ? '/get-profile-photos/'+invite.userPhoto : '/user.png'"
                                        :class="!invite.userPhoto && 'contrast'"
                                        alt="..."
                                    >
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-800">{{ invite.name }}</span>
                                    <span class="text-xs text-gray-600">{{ invite.email }}</span>
                                </div>
                            </div>
                            <span class="text-xs w-24 text-center text-gray-500">{{ getFormattedDate(invite.created_at) }}</span>
                            <button @click="deleteTeamInvitation(invite.id)" v-if="$page.props.user.id == $page.props.user.current_team.owner.id" type="button" class="p-1.5 ml-2 text-gray-400 rounded-full hover:text-white bg-gray-300 hover:bg-red-500 focus:outline-none focus:ring">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </div>
                    </li>
                </ul>

                <div v-if="currentComponent === 'group'" class="pt-3 mt-2 border-gray-200">

                    <ul class="flex flex-row items-center justify-between p-1 bg-gray-300 rounded-lg">
                        <li
                            @click="changeCurrentGroupStorage('media')"
                            class="px-3 py-1 text-xs font-semibold rounded-md cursor-pointer"
                            :class="currentGroupStorage === 'media' ? 'bg-white' : ''"
                        >Media</li>
<!--                        <li-->
<!--                            @click="changeCurrentGroupStorage('links')"-->
<!--                            class="px-3 py-1 text-xs font-semibold rounded-md cursor-pointer"-->
<!--                            :class="currentGroupStorage === 'links' ? 'bg-white' : ''"-->
<!--                        >Links</li>-->
                        <li
                            @click="changeCurrentGroupStorage('files')"
                            class="px-3 py-1 text-xs font-semibold rounded-md cursor-pointer"
                            :class="currentGroupStorage === 'files' ? 'bg-white' : ''"
                        >Files</li>
                        <li
                            v-if="$page.props.user.current_team.team_type === 'group'"
                            @click="changeCurrentGroupStorage('users')"
                            class="px-3 py-1 text-xs font-semibold rounded-md cursor-pointer"
                            :class="currentGroupStorage === 'users' ? 'bg-white' : ''"
                        >Users</li>
                    </ul>

<!--                    <ul v-show="currentGroupStorage === 'media' && currentComponent === 'group'" class="grid grid-cols-3 gap-2 my-3">-->
<!--                        <li v-for="file in mappedMediaFiles" :key="file.id">-->
<!--                            <img-->
<!--                                v-if="(file.extension === 'jfif' || file.extension === 'jpg' || file.extension === 'jpeg' || file.extension === 'png')-->
<!--                                && file.team_id === $page.props.user.current_team_id"-->
<!--                                class="object-cover w-40 h-32 rounded-md"-->
<!--                                alt="Img"-->
<!--                                :src="`/image-get/${file.filename}`"-->
<!--                            >-->
<!--                        </li>-->
<!--                    </ul>-->

                    <group-media-files
                        v-if="currentGroupStorage === 'media' && currentComponent === 'group'"
                        :mappedMediaFiles="mappedMediaFiles"
                        :currentGroupStorage="currentGroupStorage"
                        :currentComponent="currentComponent"
                        @groupMediaClicked="openGroupMediaImg"
                    />

                    <div v-if="mappedMediaFiles && mappedMediaFiles.length === 0 && currentGroupStorage === 'media' && currentComponent === 'group'" class="w-full mt-16 h-full flex items-center justify-center">
                        <img src="/mediaill.svg" alt="..." style="opacity: 0.6; width: 18rem;">
                    </div>

                    <div v-if="mappedGroupFiles && mappedGroupFiles.length === 0 && currentGroupStorage === 'files' && currentComponent === 'group'" class="w-full mt-16 h-full flex items-center justify-center">
                        <img src="/filesill.svg" alt="..." style="opacity: 0.6; width: 22rem;">
                    </div>

                    <!-- Group Users -->
                    <div v-if="currentGroupStorage === 'users' && currentComponent === 'group'" class="px-5 my-4">
                        <ul v-if="$page.props.user.current_team && $page.props.user.current_team.users">
                            <li class="flex flex-row my-3">
                                <div class="relative flex w-full">
                                    <div class="mr-4">
                                    <img :src="$page.props.user.current_team.owner.profile_photo_path ? '/get-profile-photos/'+$page.props.user.current_team.owner.profile_photo_path : '/user.png'"
                                         class="object-cover w-10 h-10 mr-4 bg-gray-700 rounded-full"
                                         alt="img"
                                         :class="!$page.props.user.current_team.owner.profile_photo_path && 'contrast'"
                                    >
                                    <span v-if="$page.props.user.current_team.owner.status === 'online'" class="absolute bottom-0 w-4 h-4 bg-green-400 rounded-full left-8"></span>
                                    </div>

                                    <div class="flex items-center justify-between w-full">
                                        <div class="flex flex-col justify-center">
                                            <h2 class="text-sm font-bold">{{ $page.props.user.current_team.owner.name }} <span class="text-xs text-blue-400">(administrator)</span></h2>
                                            <p class="text-xs font-normal text-gray-600">{{ $page.props.user.current_team.owner.email }}</p>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li v-for="user in $page.props.user.current_team.users" :key="user.id" class="flex flex-row my-3">
                                <div class="relative flex items-center w-full">
                                    <div class="mr-4">
                                        <img :src="user.profile_photo_path ? '/get-profile-photos/'+user.profile_photo_path : '/user.png'"
                                             class="object-cover w-10 h-10 mr-6 bg-gray-700 rounded-full"
                                             alt=""
                                             :class="!user.profile_photo_path && 'contrast'"
                                        >
                                        <span v-if="user.status === 'online'" class="absolute bottom-0 w-4 h-4 bg-green-400 rounded-full left-8"></span>
                                    </div>

                                    <div class="flex items-center justify-between w-full">
                                        <div class="flex flex-col justify-center">
                                            <h2 class="text-sm font-bold">{{ user.name }}</h2>
                                            <p class="text-xs font-normal text-gray-600">{{ user.email }}</p>
                                        </div>
                                    </div>

                                    <button
                                        v-if="$page.props.user.id === $page.props.user.current_team.owner.id"
                                        @click="openDeleteUserModal(user)"
                                        type="button"
                                        class="flex flex-col items-center justify-center w-8 h-8 p-1 text-gray-400 rounded-full hover:text-gray-600 hover:bg-gray-300 focus:outline-none focus:ring"
                                    >
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" data-v-563f9c74="">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" data-v-563f9c74=""></path>
                                        </svg>
                                    </button>
                                </div>
                            </li>
                        </ul>
                        <jet-dialog-modal :show="showDeleteUserModal" @close="closeDeleteUserModal">
                            <template #content>
                                <div class="justify-center flex-auto p-5 text-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="flex items-center w-6 h-6 mx-auto -m-1 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="flex items-center w-16 h-16 mx-auto text-red-500" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                    <h2 class="py-4 text-xl font-bold ">Are you sure?</h2>
                                        <p class="px-8 text-sm text-gray-500">Do you really want to remove {{ deleteUser.name }} from your group?</p>
                                </div>
                            </template>
                            <template #footer>
                                <div class="p-3 mt-2 space-x-4 text-center md:block">
                                    <button @click="closeDeleteUserModal" class="px-5 py-2 mb-2 text-sm font-medium tracking-wider text-gray-600 bg-white border rounded-full shadow-sm md:mb-0 hover:shadow-lg hover:bg-gray-100">
                                        Cancel
                                    </button>
                                    <button type="button" @click="deleteTeamUser" class="px-5 py-2 mb-2 text-sm font-medium tracking-wider text-white bg-red-500 border border-red-500 rounded-full shadow-sm md:mb-0 hover:shadow-lg hover:bg-red-600">Delete</button>
                                </div>
                            </template>
                    </jet-dialog-modal>
                    </div>
                </div>
                <ul v-if="currentGroupStorage === 'files' && currentComponent === 'group'" class="grid grid-cols-3 gap-2 my-3">
                    <li v-for="file in mappedGroupFiles" :key="file.id" class="">
                        <a
                            :href="getFileGroupType() + file.filename"
                            target="_blank"
                            class="flex flex-col items-center justify-center mt-3 text-xs font-semibold"
                            v-if="file.team_id === $page.props.user.current_team_id"
                        >
                            <div class="p-2 rounded-full hover:bg-gray-200 bg-gray-100 shadow-md flex items-center justify-center">
                                <img v-if="file.extension === 'pdf'" src="/pdf.png" alt="pdf" class="w-8 h-8 m-2" />
                                <img v-if="file.extension === 'xlsx'" src="/excel.png" alt="Excell" class="w-8 h-8 m-2" />
                                <img v-if="file.extension === 'doc'" src="/word.png" alt="Word" class="w-8 h-8 m-2" />
                                <img v-if="file.extension === 'rtf' || file.extension === 'txt'" src="/doc.png" alt="Document" class="w-8 h-8 m-2" />
                            </div>

                            <span class="mt-1">{{ reduceFileName(file.original_filename) }}</span>
                        </a>
                    </li>
                </ul>
            </main>
        </div>

</template>

<script>
import InviteToGroup from "@/Components/InviteToGroup";
import JetDialogModal from '@/Jetstream/DialogModal';
import GroupMediaFiles from "@/Components/GroupMediaFiles";
import moment from "moment";

export default {
    name: "RightSection",
    components: {
        InviteToGroup,
        JetDialogModal,
        GroupMediaFiles
    },
    props: {
        changeCurrentLeftSide: Function,
        rightSideComponent: String,
        groupFiles: Array,
        groupMedia: Array,
        lastNewPrivateMessage: Object,
        gotNewMessage: Object,
        setNewGroupsCopy: Function,
        setGroupMediaOpenedImg: Function,
        removeMappedTopics: Function,
        closeRightSide: Function,
        removeCurrentTopicAfterDelete: Function
    },
    data() {
        return {
            currentGroupStorage: 'media',
            currentComponent: 'group',
            editTeamName: false,
            showDeleteModal: false,
            showDeleteUserModal: false,
            deleteUser: null,
            mappedGroupFiles: [],
            mappedMediaFiles: [],
            mappedTeamInvitations: [],
            form: this.$inertia.form({
                name: ""
            }),
            photoForm: this.$inertia.form({
                team_id: "",
                photo: null
            }),

        }
    },
    methods: {
        openGroupMediaImg(url) {
            this.setGroupMediaOpenedImg(url)
        },
        getFileGroupType() {
            return this.$page.props.user.current_team.team_type === 'group' ? '/download-topic-file/' : '/download/'
        },
        changeCurrentGroupStorage(storage) {
            localStorage.setItem('cgs', storage);
            this.currentGroupStorage = storage;
        },
        changeCurrentComponent(component) {
            localStorage.setItem('cgc', component);
            this.currentComponent = component;
        },
        editingTeamName() {
            this.form.name = this.$page.props.user.current_team.name;
            this.editTeamName = true;
        },
        doneEditingTeamName() {
            this.editTeamName = false;
        },
        openDeleteModal() {
            this.showDeleteModal = true;
        },
        openDeleteUserModal(user) {
            this.showDeleteUserModal = true;
            this.deleteUser = user;
        },
        reduceFileName(filename) {
          if (filename.length > 15) {
              return filename.substring(0, 15)+"...";
          } else {
              return filename;
          }
        },
        getFormattedDate(date) {
            return moment(date).format("DD MMM YYYY H:m")
        },
        closeDeleteModal() {
            this.showDeleteModal = false;
        },
        closeDeleteUserModal() {
            this.showDeleteUserModal = false;
            this.deleteUser = null;
        },
        changeTeamName() {
            if (this.$page.props.user.id === this.$page.props.user.current_team.owner.id) {
                this.form.put(route('teams.update', this.$page.props.user.current_team), {
                    errorBag: 'updateTeamName',
                    preserveScroll: true,
                    onSuccess: () => {
                        this.form.reset();
                        this.editTeamName = false;
                    }
                });
            }
        },
        leaveConversation() {
            localStorage.setItem('cr_side', '');
            this.$inertia.visit(route('leave.conversation', { userId: this.$page.props.user.id }), {
                method: 'post',
                preserveState: false,
                onSuccess: () => {
                    this.closeRightSide();
                }
            })
        },
        leaveGroup() {
            localStorage.setItem('cr_side', '');
            this.$inertia.visit(route('leave.group'), {
                method: 'post',
                preserveState: false,
                onSuccess: () => {
                    this.closeRightSide();
                }
            });
        },
        deleteTeam() {
            localStorage.setItem('cr_side', '');
            if (this.$page.props.user.current_team && this.$page.props.user.id === this.$page.props.user.current_team.owner.id) {
                this.$inertia.delete(route('custom.delete.group', {teamId: this.$page.props.user.current_team_id}), {
                    errorBag: 'deleteTeam',
                    onSuccess: () => {
                        this.showDeleteModal = false;
                        this.closeRightSide();
                        this.removeMappedTopics();
                        this.removeCurrentTopicAfterDelete();
                        this.changeCurrentLeftSide("");
                        // this.setNewGroupsCopy(res.data);
                    }
                });
                // window.axios.delete(`/test-delete-group?teamId=${this.$page.props.user.current_team_id}`).then(res => {
                //     this.showDeleteModal = false;
                //     this.changeCurrentLeftSide("");
                //     this.setNewGroupsCopy(res.data);
                // });
            }
        },
        setNewTeamInvitation(invitations) {
            this.mappedTeamInvitations = invitations;
        },
        deleteTeamUser() {
            if (this.$page.props.user.current_team && this.$page.props.user.id === this.$page.props.user.current_team.owner.id) {
                this.$inertia.delete(route('team-members.destroy', {team: this.$page.props.user.current_team, user:this.deleteUser.id}), {
                    errorBag: 'deleteTeamUser',
                    onSuccess: (result) => {
                        this.showDeleteUserModal = false;
                    }
                });
            }
        },
        deleteTeamInvitation(invitation_id) {
            this.$inertia.delete(route('team-invitations.delete', {id: invitation_id}), {
                onSuccess: (result) => {
                    this.mappedTeamInvitations = result.props.user.current_team.team_invitations;
                }
            });
        },
        selectNewPhoto() {
            this.$refs.photo.click();
        },
        uploadGroupPhoto() {
            this.photoForm.team_id = this.$page.props.user.current_team;
            this.photoForm.photo = this.$refs.photo.files[0];
            this.photoForm.post(route('group.photo'), {
                preserveScroll: true,
                onSuccess: () => {
                    this.photoForm.reset();
                },
                onError: () => {
                    console.log(this.$page.props.errors);
                }
            });
        }
    },
    watch: {
        rightSideComponent: {
            immediate: true,
            deep: true,
            handler(newValue, oldValue) {
                if (newValue === 'invite-to-group') {
                    this.currentComponent = newValue;
                }
            }

        },
        lastNewPrivateMessage: {
            immediate: true,
            deep: true,
            handler(newValue, oldValue) {
                if (newValue && newValue.files && newValue.files.length > 0) {
                    newValue.files.forEach(file => {
                        if (file.extension === 'pdf' ||
                            file.extension === 'xlsx' ||
                            file.extension === 'rtf' ||
                            file.extension === 'doc' ||
                            file.extension === 'txt')
                        {
                            this.mappedGroupFiles.push(file);
                        }

                        if (file.extension === 'jpeg' || file.extension === 'jpg' || file.extension === 'png' || file.extension === 'jfif') {
                            this.mappedMediaFiles.push(file);
                        }
                    })
                }
            }
        },
        gotNewMessage: {
            immediate: true,
            deep: true,
            handler(newValue, oldValue) {
                if (newValue && newValue.files && newValue.files.length > 0) {
                    newValue.files.forEach(file => {
                        if (file.extension === 'pdf' ||
                            file.extension === 'xlsx' ||
                            file.extension === 'rtf' ||
                            file.extension === 'doc' ||
                            file.extension === 'txt')
                        {
                            this.mappedGroupFiles.push(file);
                        }

                        if (file.extension === 'jpeg' || file.extension === 'jpg' || file.extension === 'png' || file.extension === 'jfif') {
                            this.mappedMediaFiles.push(file);
                        }
                    })
                }
            }
        },
    },
    mounted() {
        let cgs = localStorage.getItem('cgs');
        let cgc = localStorage.getItem('cgc');
        if (cgs) {
            if (cgs === 'media') {
                this.currentGroupStorage = 'users';
            } else {
                this.currentGroupStorage = cgs;
            }
        }
        if (this.$page.props.user.current_team && cgc && this.$page.props.user.current_team.team_type === 'group') {
            if (this.$page.props.user.current_team.name !== 'not-showing') {
                this.currentComponent = cgc;
            } else {
                this.currentComponent = '';
            }
        }
        if (cgc === 'invite-to-group' && this.$page.props.user.id !== this.$page.props.user.current_team.owner.id) {
            this.currentComponent = 'group';
            localStorage.setItem('cgc', 'group');
        }
        if (cgc === 'pending-invites' && this.$page.props.user.id !== this.$page.props.user.current_team.owner.id) {
            this.currentComponent = 'group';
            localStorage.setItem('cgc', 'group');
        }
        if (this.$page.props.user.current_team && this.$page.props.user.current_team.team_type === 'private-message') {
            this.currentGroupStorage = 'files';
        }
        this.mappedGroupFiles = this.groupFiles ? Object.values(this.groupFiles) : [];
        this.mappedMediaFiles = this.groupMedia ? Object.values(this.groupMedia) : [];
        if (this.$page.props.user.current_team) {
            this.mappedTeamInvitations = this.$page.props.user.current_team.team_invitations;
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
    .contrast {
        filter: grayscale(1) invert(1);
    }
</style>
