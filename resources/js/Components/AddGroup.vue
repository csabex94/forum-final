<template>
    <div
        :class="currentRightSide === 'add-group' ? 'slideIn' : 'slideOut'"
         class="absolute flex flex-col w-full items-stretch  justify-start p-3 rounded-md addGroup bg-gray-50 lg:rounded-none lg:rounded-l-md">
        <div class="flex flex-col flex-auto">
            <div class="flex flex-col relative flex-auto">
                <div class="w-full p-1">
                    <form @submit.prevent="createTeam" autoComplete="off">
                        <div class="flex items-start w-full p-1">
                            <input autocomplete="off" autocorrect="off" id="groupName" v-model="form.name" class="w-full py-1 text-sm text-gray-700 bg-gray-200 border-gray-300 rounded focus:ring-transparent focus:border-gray-500" type="text" placeholder="Group name" />
                            <button type="button" @click="changeCurrentRightSide('groups')" class="flex flex-col items-center justify-center p-2 ml-2 rounded-full focus:ring-2 hover:bg-gray-200 hover:bg-opacity-30 focus:outline-none" aria-label="Add">
                                <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                    <path d="M15.898,4.045c-0.271-0.272-0.713-0.272-0.986,0l-4.71,4.711L5.493,4.045c-0.272-0.272-0.714-0.272-0.986,0s-0.272,0.714,0,0.986l4.709,4.711l-4.71,4.711c-0.272,0.271-0.272,0.713,0,0.986c0.136,0.136,0.314,0.203,0.492,0.203c0.179,0,0.357-0.067,0.493-0.203l4.711-4.711l4.71,4.711c0.137,0.136,0.314,0.203,0.494,0.203c0.178,0,0.355-0.067,0.492-0.203c0.273-0.273,0.273-0.715,0-0.986l-4.711-4.711l4.711-4.711C16.172,4.759,16.172,4.317,15.898,4.045z"></path>
                                </svg>
                            </button>
                        </div>
                    </form>
                </div>
                <div v-show="loading" class="w-full absolute spinnerPosition mt-16 flex items-center justify-center">
                    <loading-spinner />
                </div>
                <div class="mt-16 w-full flex items-center justify-center">
                    <img src="/addgroupill.svg" alt="addgroupill" style="opacity: .6; width: 22rem">
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import LoadingSpinner from '@/Components/LoadingSpinner';

    export default {
        components: {
            LoadingSpinner
        },
        props: {
            changeCurrentRightSide: Function,
            currentRightSide: String,
            setNewGroups: Function,
            setMappedGroupFiles: Function,
            setMappedGroupMedia: Function
        },
        data() {
            return {
                form: this.$inertia.form({
                    name: '',
                    teamType: 'group'
                }),
                loading: false
            }
        },
        methods: {
            createTeam() {
                this.loading = true;
                this.form.post(route('teams.store'), {
                    errorBag: 'createTeam',
                    preserveScroll: true,
                    onSuccess: (result) => {
                        this.changeCurrentRightSide('groups');
                        localStorage.setItem('cl_s', 'groups');
                        this.form.reset();
                        this.setNewGroups(Object.values(result.props.all_teams));
                        this.setMappedGroupFiles([]);
                        this.setMappedGroupMedia([]);
                        this.loading = false;
                    }
                })
            },
        },
    }
</script>

<style scoped>
    .addGroup {
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
    .spinnerPosition {
        top: 3rem;
        z-index: 60;
    }
</style>
