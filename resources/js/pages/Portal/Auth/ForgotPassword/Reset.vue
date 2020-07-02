<template>
    <CContainer class="d-flex content-center min-vh-100">
        <CRow v-if="!resetSuccess">
            <CCol>
                <CCardGroup>
                    <CCard class="p-4">
                        <CCardBody>
                            <ValidationObserver
                                v-slot="{ handleSubmit }"
                                ref="form"
                            >
                                <form
                                    class="form-custom"
                                    @submit.prevent="handleSubmit(reset)"
                                >
                                    <p class="form-title">
                                        パスワードの再設定
                                    </p>
                                    <p class="reset-info">
                                        ユーザーの確認ができましたので新しいパスワードをご入力下さい。
                                    </p>
                                    <ValidationProvider
                                        v-slot="{ errors }"
                                        vid="password"
                                        name="パスワード"
                                        rules="required|min:8"
                                        mode="passive"
                                    >
                                        <div
                                            role="group"
                                            class="form-group"
                                        >
                                            <input
                                                v-model="formData.password"
                                                type="password"
                                                placeholder="パスワード"
                                                :class="['form-control', errors[0] ? 'is-invalid' : '']"
                                            >
                                            <div
                                                v-if="errors[0]"
                                                class="invalid-feedback"
                                            >
                                                {{ errors[0] }}
                                            </div>
                                        </div>
                                    </ValidationProvider>

                                    <ValidationProvider
                                        v-slot="{ errors }"
                                        vid="password_confirmation"
                                        name="パスワード（確認用）"
                                        rules="required|min:8"
                                        mode="passive"
                                    >
                                        <div
                                            role="group"
                                            class="form-group"
                                        >
                                            <input
                                                v-model="formData.password_confirmation"
                                                type="password"
                                                placeholder="パスワード（確認用）"
                                                :class="['form-control', errors[0] ? 'is-invalid' : '']"
                                            >
                                            <div
                                                v-if="errors[0]"
                                                class="invalid-feedback"
                                            >
                                                {{ errors[0] }}
                                            </div>
                                        </div>
                                    </ValidationProvider>
                                    <div class="row">
                                        <div class="text-left col-4">
                                            <button
                                                type="submit"
                                                class="btn px-4 text-nowrap btn-primary auth-btn"
                                            >
                                                登録
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </ValidationObserver>
                        </CCardBody>
                    </CCard>
                </CCardGroup>
            </CCol>
        </CRow>
        <ResetSuccess v-else />
    </CContainer>
</template>

<script>
import { ValidationObserver, ValidationProvider } from 'vee-validate';
import { mapActions } from 'vuex';
import ResetSuccess from '~/components/Portal/Auth/ForgotPassword/ResetSuccess';
export default {
    name: 'Login',
    components: {
        ResetSuccess,
        ValidationObserver,
        ValidationProvider
    },
    data: () => ({
        formData: {
            password: '',
            password_confirmation: ''
        },
        errorsRes: false,
        resetSuccess: false
    }),
    methods: {
        ...mapActions('auth', [
            'forgotReset'
        ]),

        ...mapActions('layout', [
            'setLoading'
        ]),

        async reset() {
            this.setLoading(true);
            let formData = { ...this.formData, token: this.$route.params.token };
            try {
                await this.forgotReset(formData);
            } catch(e) {
                this.setLoading(false);
                return;
            }
            this.setLoading(false);
            this.resetSuccess = true;
        }
    }
};
</script>

<style lang="scss" scoped>
    .reset-info {
        font-size: 13px;
        margin-bottom: 1rem;
        text-align: left;
    }
</style>
