<template>
    <li>
        <div class="flex items-center justify-between mb-4 bg-gray-100 rounded-md shadow p-2" v-if="user.id !== $page.props.user.id">
            <div class="flex items-center">
                <div class="mr-2">
                    <img
                        :src="user.profile_photo_path ? `/get-profile-photos/${user.profile_photo_path}` : '/user.png'"
                        :class="!user.profile_photo_path && 'contrast'"
                        class="object-cover w-12 h-12 mr-4 bg-gray-700 rounded-full"
                        alt="img">
                </div>
                <div class="flex items-center justify-between w-full">
                    <div class="flex flex-col justify-center">
                        <h2 class="text-sm font-bold">{{ user.name }}</h2>
                        <p class="text-xs font-normal text-gray-600">{{ user.email }}</p>
                    </div>
                </div>
            </div>

            <div class="flex items-center">
                <label class="inline-flex items-center mr-2">
                    <jet-checkbox permissionType="readonly" :checked="readonlyChecked" @update:checked="handleUserPermissionTopic" />
                    <span class="ml-2 text-xs font-bold text-gray-700">Readonly</span>
                </label>
                <label class="inline-flex items-center">
                    <jet-checkbox permissionType="public" :checked="publicChecked" @update:checked="handleUserPermissionTopic" />
                    <span class="ml-2 text-xs font-bold text-gray-700">Public</span>
                </label>
            </div>
        </div>
    </li>
</template>

<script>
import JetCheckbox from '@/Jetstream/Checkbox';

export default {
    name: "TopicPermissionUser",
    components: {
        JetCheckbox
    },
    props: {
        user: Object,
        permission: Object
    },
    data() {
        return {
            readonlyChecked: false,
            publicChecked: false
        }
    },
    methods: {
        handleUserPermissionTopic(data) {
            if (data.permissionType === 'readonly') {
                this.readonlyChecked = data.value;
                this.publicChecked = false;
                this.$emit('permission:updated', { user: this.user, permission: 'readonly' });
            }
            if (data.permissionType === 'public') {
                this.publicChecked = data.value;
                this.readonlyChecked = false;
                this.$emit('permission:updated', { user: this.user, permission: 'public' });
            }
        }
    },
    mounted() {
        if (this.permission && this.user.id === this.permission.userId) {
            if (this.permission.permission === 'public') {
                this.publicChecked = true;
                this.readonlyChecked = false;
            }
            if (this.permission.permission === 'readonly') {
                this.publicChecked = false;
                this.readonlyChecked = true;
            }
        }
    }
}
</script>

<style scoped>

</style>
