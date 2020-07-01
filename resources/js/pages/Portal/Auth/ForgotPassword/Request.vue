<template>
    <CContainer class="d-flex content-center min-vh-100">
        <CRow v-if="!requestSuccess">
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
                                    @submit.prevent="handleSubmit(submitRequest)"
                                >
                                    <p class="form-title">
                                        パスワードの再発行
                                    </p>
                                    <div class="text-left email-info">
                                        <p>登録済みのメールアドレスをご入力下さい。</p>
                                        <p>ご入力いただいたメールアドレス宛にパスワード再発行の</p>
                                        <p>お手続きに関するメールをお送りいたします。</p>
                                    </div>
                                    <ValidationProvider
                                        v-slot="{ errors }"
                                        vid="email"
                                        name="メールアドレス"
                                        rules="required|email"
                                        mode="passive"
                                    >
                                        <div
                                            role="group"
                                            class="form-group"
                                        >
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">
                                                        <svg
                                                            xmlns="http://www.w3.org/2000/svg"
                                                            width="16"
                                                            height="12"
                                                            viewBox="0 0 16 12"
                                                        >
                                                            <path
                                                                id="Path_34"
                                                                data-name="Path 34"
                                                                d="M15.7,67.962a.188.188,0,0,1,.3.147V74.5A1.5,1.5,0,0,1,14.5,76H1.5A1.5,1.5,0,0,1,0,74.5V68.113a.187.187,0,0,1,.3-.147c.7.544,1.628,1.234,4.816,3.55C5.778,72,6.891,73.009,8,73c1.116.009,2.25-1.025,2.884-1.488C14.072,69.2,15,68.506,15.7,67.962ZM8,72c.725.013,1.769-.912,2.294-1.294,4.147-3.009,4.463-3.272,5.419-4.022A.748.748,0,0,0,16,66.094V65.5A1.5,1.5,0,0,0,14.5,64H1.5A1.5,1.5,0,0,0,0,65.5v.594a.752.752,0,0,0,.287.591c.956.747,1.272,1.012,5.419,4.022C6.231,71.088,7.275,72.013,8,72Z"
                                                                transform="translate(0 -64)"
                                                                fill="#777"
                                                            />
                                                        </svg>
                                                    </span>
                                                </div>
                                                <input
                                                    v-model.trim="formData.email"
                                                    type="email"
                                                    placeholder="メールアドレス"
                                                    :class="['form-control', errors[0] ? 'is-invalid' : '']"
                                                >
                                                <div
                                                    v-if="errors[0]"
                                                    class="invalid-feedback"
                                                >
                                                    {{ errors[0] }}
                                                </div>
                                            </div>
                                        </div>
                                    </ValidationProvider>
                                    <div class="row">
                                        <div class="text-left col-4">
                                            <button
                                                type="submit"
                                                class="btn px-4 text-nowrap btn-primary auth-btn"
                                            >
                                                送信
                                            </button>
                                        </div>
                                        <div class="text-right col-8">
                                            <button
                                                type="button"
                                                class="btn px-0 text-nowrap btn-link" 
                                            >
                                                <router-link
                                                    :to="{name: 'portal.login'}"
                                                    class="auth-forward router-link-active"
                                                >
                                                    ログイン画面に戻る
                                                </router-link>
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
        <RequestSuccess v-else />
    </CContainer>
</template>

<script>
import { ValidationObserver, ValidationProvider } from 'vee-validate';
import { mapState, mapActions } from 'vuex';
import RequestSuccess from '~/components/Portal/Auth/ForgotPassword/RequestSuccess';

export default {
    name: 'PortalRequestPassword',
    components: {
        RequestSuccess,
        ValidationObserver,
        ValidationProvider
    },
    data: () => ({
        formData: {
            email: ''
        },
        requestSuccess: false
    }),
    computed: {
        ...mapState('auth', {
            forgotRes: state => state.forgotRes
        })
    },
    methods: {
        ...mapActions('auth', ['forgotRequest']),
        ...mapActions('layout', ['setLoading']),

        async submitRequest() {
            this.setLoading(true);
            try {
                await this.forgotRequest(this.formData);   
            } catch(e) {
                this.setLoading(false);
                return;
            }
            this.setLoading(false);
            this.requestSuccess = true;
        }
    }
};
</script>

<style lang="scss" scoped>
    .email-info {
        font-size: 13px;
        margin-bottom: 1rem;
            p {
                margin-bottom: 0.25rem !important;
            }
    }
</style>
