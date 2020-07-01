<template>
    <CContainer class="d-flex content-center min-vh-100">
        <CRow>
            <CCol>
                <CCardGroup>
                    <CCard class="p-4">
                        <CCardBody>
                            <CForm class="form-custom">
                                <div class="c-success__body">
                                    <div class="c-success__txt">
                                        <span>パスワード再設定用のURLを記載した<br>
                                            メールを送信しました。</span>
                                    </div>
                                </div>
                                <div class="c-success__footer text-left">
                                    <span>パスワード再設定用のURLを押してパスワードの再設定を行ってください。</span>
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
    name: 'PortalRequestSuccess'
};
</script>

<style lang="scss">
    .form-custom {
        width: 280px;

        .c-success__body {
            padding-bottom: 2.4rem;
            border-bottom: 0.1rem solid #E3E3E3;

            .c-success__txt {
                letter-spacing: 0;
                font-weight: bold;
            }
        }

        .c-success__footer {
            padding-top: 2.4rem;
        }
    }
    
</style>
