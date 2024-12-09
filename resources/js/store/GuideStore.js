export default {
    state: {
        guideInfo: {},
        guideId: null,
        editGuideInfo: {},
        voucherInfo: [],
        profilePic: "",
        ssc_certificate_link: "",
        hsc_certificate_link: "",
        honours_certificate_link: "",
        masters_certificate_link: "",
    },
    getters: {
        guideInfo: state => state.guideInfo,
        getGuideId(state) {
            return state.guideId
        },
        editGuideInfo: state => state.editGuideInfo,
        voucherInfo: state => state.voucherInfo,
        profilePic: state => state.profilePic,
        ssc_certificate_link: state => state.ssc_certificate_link,
        hsc_certificate_link: state => state.hsc_certificate_link,
        honours_certificate_link: state => state.honours_certificate_link,
        masters_certificate_link: state => state.masters_certificate_link,
    },
    mutations: {
        setGuideInfo(state, guideInfo) {
            state.guideInfo = guideInfo;
        },
        resetGuideInfo(state) {
            state.guideInfo = {};
        },
        setGuideId(state, payload) {
            state.guideId = payload
        },
        setEditGuideInfo(state, editGuideInfo) {
            state.editGuideInfo = editGuideInfo;
        },
        setVoucherInfo(state, voucherInfo) {
            state.voucherInfo = voucherInfo;
        },
        setProfilePic(state, profilePic) {
            state.profilePic = profilePic;
        },
        setSscCertificateLink(state, sscCertificateLink) {
            state.ssc_certificate_link = sscCertificateLink;
        },
        setHscCertificateLink(state, hscCertificateLink) {
            state.hsc_certificate_link = hscCertificateLink;
        },
        setHonoursCertificateLink(state, honoursCertificateLink) {
            state.honours_certificate_link = honoursCertificateLink;
        },
        setMastersCertificateLink(state, mastersCertificateLink) {
            state.masters_certificate_link = mastersCertificateLink;
        }
    },
    actions: {
        updateGuideInfo({ commit }, guideInfo) {
            commit('setGuideInfo', guideInfo);
        },
        resetGuideInfo({ commit }, guideInfo) {
            commit('resetGuideInfo', guideInfo);
        },
        updateGuideId({ commit }, guideId) {
            commit('setGuideId', guideId);
        },
        updateEditGuideInfo({ commit }, editGuideInfo) {
            commit('setEditGuideInfo', editGuideInfo);
        },
        updateVoucherInfo({ commit }, voucherInfo) {
            commit('setVoucherInfo', voucherInfo);
        },
        updateProfilePic({ commit }, profilePic) {
            commit('setProfilePic', profilePic);
        },
        updateSscCertificateLink({ commit }, sscCertificateLink) {
            commit('setSscCertificateLink', sscCertificateLink);
        },
        updateHscCertificateLink({ commit }, hscCertificateLink) {
            commit('setHscCertificateLink', hscCertificateLink);
        },
        updateHonoursCertificateLink({ commit }, honoursCertificateLink) {
            commit('setHonoursCertificateLink', honoursCertificateLink);
        },
        updateMastersCertificateLink({ commit }, mastersCertificateLink) {
            commit('setMastersCertificateLink', mastersCertificateLink);
        },
    }
}
