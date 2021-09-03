<template>
    <div class="flex items-center min-h-screen bg-white dark:bg-gray-900">
        <div class="container mx-auto">
            <div class="max-w-md mx-auto">
                <div class="text-center">
                    <div class="w-full h-full flex items-center justify-center">
                        <img src="/loginill.svg" alt="..." class="illSize" style="opacity: 0.6;">
                    </div>
                    <h1 class="my-3 text-3xl font-semibold text-gray-700 dark:text-gray-200">Sign in</h1>
                    <p class="text-gray-500 dark:text-gray-400">Sign in to access your account</p>
                </div>
                <jet-validation-errors class="mb-4" />

                <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
                    {{ status }}
                </div>
                <div class="m-7">
                    <form @submit.prevent="submit">
                        <div class="mb-6">
                            <jet-label for="email" class="block mb-2 text-sm text-gray-600 dark:text-gray-400" value="Email" />
                            <jet-input id="email" type="email" class="w-full px-5  py-4 text-gray-700 bg-gray-200 rounded" v-model="form.email" required autofocus />
                        </div>
                        <div class="mb-2">
                            <jet-label for="password" class="text-sm text-gray-600 dark:text-gray-400" value="Password" />
                            <jet-input id="password"  type="password" class="w-full px-5  py-4 text-gray-700 bg-gray-200 rounded" v-model="form.password" required autocomplete="current-password" />
                        </div>
                        <inertia-link v-if="canResetPassword" :href="route('password.request')" class="text-sm my-1 text-gray-400 focus:outline-none focus:text-indigo-500 hover:text-indigo-500 dark:hover:text-indigo-300">
                            Forgot your password?
                        </inertia-link>
                        <div class="mt-3">
                            <button :class="{ 'opacity-25': form.processing }" :disabled="form.processing" class="w-full px-3 py-4 text-white bg-indigo-500 rounded-md focus:bg-indigo-600 focus:outline-none">Sign in</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</template>

<script>
    import JetAuthenticationCard from '@/Jetstream/AuthenticationCard'
    import JetAuthenticationCardLogo from '@/Jetstream/AuthenticationCardLogo'
    import JetButton from '@/Jetstream/Button'
    import JetInput from '@/Jetstream/Input'
    import JetCheckbox from '@/Jetstream/Checkbox'
    import JetLabel from '@/Jetstream/Label'
    import JetValidationErrors from '@/Jetstream/ValidationErrors'
    import Input from "@/Jetstream/Input";

    export default {
        components: {
            Input,
            JetAuthenticationCard,
            JetAuthenticationCardLogo,
            JetButton,
            JetInput,
            JetCheckbox,
            JetLabel,
            JetValidationErrors
        },

        props: {
            canResetPassword: Boolean,
            status: String
        },

        data() {
            return {
                form: this.$inertia.form({
                    email: '',
                    password: '',
                    remember: false,
                    _token: ''
                })
            }
        },

        methods: {
            submit() {
                try {
                    window.axios.post('/log/login', {email: this.form.email}).then(function(res) {
                        console.log(res.data);
                    });
                } catch (err) {
                    console.log(err);
                } finally {
                    this.form
                        .transform(data => ({
                            ... data,
                            remember: this.form.remember ? 'on' : ''
                        }))
                        .post(this.route('login'), {
                            onFinish: () => this.form.reset('password'),
                        })
                }
            }
        }
    }
</script>

<style scoped>
    .illSize {
        width: 20rem;
    }
</style>
