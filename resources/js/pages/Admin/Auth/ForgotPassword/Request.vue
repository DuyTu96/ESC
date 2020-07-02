<template>
    <div>
        <div
            v-if="!requestSuccess"
            class="l-2col"
        >
            <main class="l-main">
                <div class="center-center m-0">
                    <div class="c-cardSetting">
                        <div class="c-cardSetting__header show">
                            <div class="l-header__icon">
                                <router-link
                                    :to="{ name: 'admin.login' }"
                                    class="l-header__link"
                                    href="#"
                                >
                                    <img
                                        class="l-header__img"
                                        src="/dist/img/ico_arrow_back.svg"
                                        alt=""
                                    >
                                </router-link>
                            </div>
                            <div class="l-header__txt">
                                パスワードを忘れた方
                            </div>
                        </div>

                        <div class="c-cardSetting__body">
                            <div class="c-cardSetting__txt">
                                <span>登録しているメールアドレスにパスワード再設定用のURLをお送りしますので、パスワード再設定URLを押してパスワードを再設定してください。</span>
                            </div>
                            <div class="c-cardSetting__form-box">
                                <ValidationObserver
                                    ref="observer"
                                    v-slot="{ invalid }"
                                >
                                    <form
                                        action=""
                                        method="post"
                                        class="c-cardSetting__form"
                                        @submit.prevent="forgotRequest"
                                    >
                                        <div class="c-cardSetting__code">
                                            <label
                                                class="c-cardSetting__label"
                                                for="mail"
                                            >登録しているメールアドレス</label>
                                            <ValidationProvider
                                                v-slot="{ errors }"
                                                name="登録しているメールアドレス"
                                                rules="required|email"
                                            >
                                                <input
                                                    v-model.trim="formData.email"
                                                    placeholder="登録しているメールアドレス"
                                                    class="c-form__txt"
                                                    :class="{error: (requestError || errors[0])}"
                                                    @keyup="changeInput"
                                                >
                                                <span
                                                    v-if="errors[0]"
                                                    class="veevalidateError"
                                                >
                                                    {{ errors[0] }}
                                                </span>
                                            </ValidationProvider>
                                        </div>
                                        <div
                                            v-if="requestError"
                                            class="c-form__alert"
                                        >
                                            <div v-if="typeof forgotRes.error.message === 'object'">
                                                <div
                                                    v-for="(errors, key) in forgotRes.error.message"
                                                    :key="key"
                                                >
                                                    <span
                                                        v-for="error in errors"
                                                        :key="error"
                                                        class="text-sm d-inline-block"
                                                    >
                                                        {{ error }}
                                                    </span>
                                                    <br>
                                                </div>
                                            </div>
                                            <span v-else>{{ forgotRes.error.message }}</span>
                                        </div>
                                        <div class="c-form__submit">
                                            <button
                                                type="submit"
                                                class="btn btn-sm btn-primary"
                                                :class="{disabled: invalid}"
                                                :disabled="invalid"
                                            >
                                                パスワード再設定
                                            </button>
                                        </div>
                                    </form>
                                </ValidationObserver>
                            </div>
                            <div class="c-cardSetting__footer">
                                <router-link
                                    :to="{ name: 'admin.login' }"
                                    href="#"
                                    class="c-cardSetting__cancel"
                                >
                                    キャンセル
                                </router-link>
                            </div>
                        </div>
                    </div>
                </div>
            </main>

            <aside class="l-side hidden">
                <figure class="l-side__logo">
                    <a
                        href="#"
                        class="l-side__logo-link"
                    ><img
                        src="/dist/img/logo_blue.svg"
                        alt="OPNID"
                    ></a>
                </figure>

                <nav class="c-sp-nav">
                    <ul class="tabBar">
                        <li class="tabBar__item">
                            <a
                                href="#"
                                class="tabBar__link"
                            >
                                <svg
                                    id="Assets_Icon_TabBar_ico_addressbook"
                                    data-name="Assets / Icon / TabBar / ico_addressbook"
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="24"
                                    height="24"
                                    viewBox="0 0 24 24"
                                >
                                    <rect
                                        id="isolation"
                                        width="24"
                                        height="24"
                                        fill="rgba(255,0,0,0)"
                                    />
                                    <path
                                        id="Fill_1"
                                        data-name="Fill 1"
                                        d="M16,20H2a2,2,0,0,1-2-2V2A2,2,0,0,1,2,0H16a2,2,0,0,1,2,2V18A2,2,0,0,1,16,20ZM4,14a1,1,0,1,0,1,1A1,1,0,0,0,4,14Zm3.7.3a.7.7,0,1,0,0,1.4h6.6a.7.7,0,1,0,0-1.4ZM4,9a1,1,0,1,0,1,1A1,1,0,0,0,4,9Zm3.7.3a.7.7,0,0,0,0,1.4h6.6a.7.7,0,1,0,0-1.4ZM4,4A1,1,0,1,0,5,5,1,1,0,0,0,4,4Zm3.7.3a.7.7,0,1,0,0,1.4h6.6a.7.7,0,1,0,0-1.4Z"
                                        transform="translate(3 2)"
                                        fill="#423d3d"
                                    />
                                </svg>
                                <span class="tabBar__link-text">アドレス帳</span>
                            </a>
                        </li>
                        <li class="tabBar__item">
                            <a
                                href="#"
                                class="tabBar__link"
                            >
                                <svg
                                    id="Assets_Icon_TabBar_ico_addressbook"
                                    data-name="Assets / Icon / TabBar / ico_addressbook"
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="24"
                                    height="24"
                                    viewBox="0 0 24 24"
                                >
                                    <rect
                                        id="isolation"
                                        width="24"
                                        height="24"
                                        fill="rgba(255,0,0,0)"
                                    />
                                    <path
                                        id="Combined_Shape"
                                        data-name="Combined Shape"
                                        d="M15.293,16.707l-4-4A1.008,1.008,0,0,1,11.2,12.6a7.007,7.007,0,1,1,1.4-1.4,1.008,1.008,0,0,1,.109.094l4,4a1,1,0,1,1-1.414,1.414ZM2,7A5,5,0,1,0,7,2,5.005,5.005,0,0,0,2,7Z"
                                        transform="translate(4 3)"
                                        fill="#423d3d"
                                    />
                                </svg>
                                <span class="tabBar__link-text">検索</span>
                            </a>
                        </li>
                        <li class="tabBar__item">
                            <a
                                href="#"
                                class="tabBar__link"
                            >
                                <svg
                                    id="Assets_Icon_TabBar_ico_addressbook"
                                    data-name="Assets / Icon / TabBar / ico_addressbook"
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="24"
                                    height="24"
                                    viewBox="0 0 24 24"
                                >
                                    <g
                                        id="Icon_TabBar_ico_account"
                                        data-name="Icon / TabBar / ico_account"
                                    >
                                        <rect
                                            id="isolation"
                                            width="24"
                                            height="24"
                                            fill="rgba(255,0,0,0)"
                                        />
                                        <path
                                            id="Combined_Shape"
                                            data-name="Combined Shape"
                                            d="M4.185,16C2.825,16,0,16,0,14c0-1.744,1.154-4,4.627-4h5.711C14.87,10,16,12.125,16,14c0,2-3.072,2-4.431,2ZM4,4A4,4,0,1,1,8,8,4,4,0,0,1,4,4Z"
                                            transform="translate(4 3)"
                                            fill="#423d3d"
                                        />
                                    </g>
                                </svg>
                                <span class="tabBar__link-text">アカウント</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </aside>
        </div>
        <RequestSuccess v-else />
    </div>
</template>

<script>
import {mapState} from 'vuex';
import {ValidationObserver, ValidationProvider} from 'vee-validate';

import RequestSuccess from '~/components/Admin/Auth/ForgotPassword/RequestSuccess';

export default {
    name: 'RequestForgotPassword',
    components: {
        ValidationObserver,
        ValidationProvider,
        RequestSuccess
    },
    data: () => ({
        formData: {
            email: ''
        },
        requestError: false,
        requestSuccess: false
    }),
    computed: {
        ...mapState('auth', {
            forgotRes: state => state.forgotRes,
        }),
    },
    methods: {
        changeInput() {
            if (this.requestError) {
                this.requestError = false;
            }
        },
        async forgotRequest() {
            const isValid = await this.$refs.observer.validate();
            if (isValid) {
                await this.$store.dispatch('auth/forgotRequest', this.formData);
                if (this.forgotRes.error) {
                    this.requestError = true;
                    return;
                }
                this.requestSuccess = true;
            }
        }
    }
};
</script>
