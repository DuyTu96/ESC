<template>
    <CRow>
        <CCol sm="3">
            <CCard>
                <CCardHeader>
                    <strong>Dương Tiến Đạt</strong> <small>Profile</small>
                </CCardHeader>
                <CCardBody>
                    <CRow>
                        <CCol sm="12">
                            <img
                                src="/dist/img/6.jpg"
                                class="img-fluid mx-auto d-block"
                            >
                        </CCol>
                    </CRow>
                    <CListGroup>
                        <CListGroupItem
                            href="#"
                            variant="secondary"
                        >
                            <CIcon name="cil-birthday-cake" />&nbsp;Birthday:
                            <span class="float-right">16/09/1997</span>
                        </CListGroupItem>
                        <CListGroupItem
                            href="#"
                            variant="secondary"
                        >
                            <CIcon name="cil-mobile" />&nbsp;Telephone:
                            <span class="float-right">0384676848</span>
                        </CListGroupItem>
                        <CListGroupItem
                            href="#"
                            variant="secondary"
                        >
                            <CIcon name="cil-building" />&nbsp;Address:
                            <span class="float-right">Hà Nội</span>
                        </CListGroupItem>
                    </CListGroup>
                </CCardBody>
            </CCard>
        </CCol>
        <CCol sm="6">
            <CCard>
                <CCardHeader>
                    <strong>Working Information</strong>
                    <div class="card-header-actions" />
                </CCardHeader>
                <CCardBody>
                    <CTabs>
                        <CTab
                            title="General"
                            active
                        >
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <th scope="row">
                                            <CIcon name="cil-user" />&nbsp;Employee ID:
                                        </th>
                                        <td>K1911</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">
                                            <CIcon name="cil-envelope-closed" />&nbsp;Email:
                                        </th>
                                        <td>datdt@fabb.io</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">
                                            <CIcon name="cil-location-pin" />&nbsp;Postion:
                                        </th>
                                        <td>
                                            <button
                                                class="btn btn-dark"
                                                type="button"
                                            >
                                                Developer
                                            </button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row">
                                            <CIcon name="cil-lan" />&nbsp;Department:
                                        </th>
                                        <td>
                                            <button
                                                class="btn btn-info"
                                                type="button"
                                            >
                                                Division 3
                                            </button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row">
                                            <CIcon name="cil-calendar" />&nbsp;Start date:
                                        </th>
                                        <td>01/08/2019</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">
                                            <CIcon name="cil-clock" />&nbsp;Work type:
                                        </th>
                                        <td>
                                            <button
                                                class="btn btn-info"
                                                type="button"
                                            >
                                                Full time
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </CTab>
                        <CTab title="Request" />
                    </CTabs>
                </CCardBody>
            </CCard>
        </CCol>
        <CCol sm="3">
            <CCard>
                <CCardHeader>
                    <strong>Project static</strong>
                    <div class="card-header-actions" />
                </CCardHeader>
                <CCardBody>
                    <CRow>
                        <CCol sm="6">
                            <CCallout color="info">
                                <span class="text-muted">Joining</span><br>
                                <strong class="h4">2</strong>
                            </CCallout>
                            <h6 class="mx-2 secondary">
                                Fabbi ECS
                            </h6>
                            <h6 class="mx-2 secondary">
                                Opnid
                            </h6>
                        </CCol>
                        <CCol sm="6">
                            <CCallout color="success">
                                <span class="text-muted">Joined</span><br>
                                <strong class="h4">4</strong>
                            </CCallout>
                            <h6 class="mx-2 secondary">
                                Legal Technology
                            </h6>
                            <h6 class="mx-2 secondary">
                                Rocktoon
                            </h6>
                            <h6 class="mx-2 secondary">
                                Skrum
                            </h6>
                            <h6 class="mx-2 secondary">
                                Anvy
                            </h6>
                        </CCol>
                    </CRow>
                </CCardBody>
            </CCard>
        </CCol>
    </CRow>
</template>
