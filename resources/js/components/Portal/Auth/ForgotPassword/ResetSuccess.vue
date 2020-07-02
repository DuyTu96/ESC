<template>
    <CContainer class="d-flex content-center min-vh-100">
        <CRow>
            <CCol>
                <CCardGroup>
                    <CCard class="p-4">
                        <CCardBody>
                            <CForm class="form-custom">
                                <div class="c-success__body">
                                    <div class="c-success__icon">
                                        <figure>
                                            <img
                                                src="/dist/img/ico_check.svg"
                                                alt=""
                                            >
                                        </figure>
                                    </div>
                                    <div class="c-success__txt-checked">
                                        <span>パスワード再設定が<br>
                                            完了しました。</span>
                                    </div>
                                </div>
                                <div class="c-success__footer">
                                    <CButton
                                        color="primary auth-btn"
                                        class="px-4 text-nowrap"
                                        @click="login"
                                    >
                                        ログイン
                                    </CButton>
                                </div>
                            </CForm>
                        </CCardBody>
                    </CCard>
                </CCardGroup>
            </CCol>
        </CRow>
    </ccontainer>
</template>

<script>
export default {
    name: 'PortalResetSuccess',
    methods: {
        login() {
            this.$router.push({ name: 'portal.login' });
        }
    }
};
</script>

<style lang="scss">
    .form-custom {
        width: 280px;

        .c-success__body {
            padding-bottom: 2.4rem;
            border-bottom: 0.1rem solid #E3E3E3;

            .c-success__txt-checked {
                margin-top: 1.6rem;
                color: #79AC13;
                letter-spacing: 0.05em;
                font-weight: bold;
            }
        }

        .c-success__footer {
            padding-top: 2.4rem;
        }
    }
    
</style>
