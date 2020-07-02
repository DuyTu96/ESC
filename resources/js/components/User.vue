<template>
    <div id="user">
        <component
            :is="layout"
            v-if="layout"
        />
    </div>
</template>

<script>
export default {
    el: '#user',

    computed: {
        /**
         * Set the application layout.
         *
         * @param {String} layout
         */
        layout() {
            return this.$route.meta.layout;
        }
    },
    methods: {
        showToast(content, type = 'success') {
            if (type == 'fail') {
                var app = this;
                if (typeof content === 'object') {
                    Object.keys(content).forEach(function(key) {
                        var childContent = content[key];
                        if (childContent.length > 1) {
                            Object.keys(childContent).forEach(function(key) {
                                app.setToast(childContent[key], 'danger');
                            });
                        } else {
                            app.setToast(childContent, 'danger');
                        }
                    });
                } else {
                    app.setToast(content, 'danger');
                }
            } else {
                this.setToast(content);
            }
        },
        setToast(content, type = 'success') {
            this.$bvToast.toast(content, {
                variant: type,
                autoHideDelay: 2000,
                appendToast: true
            });
        }
    }
};
</script>
