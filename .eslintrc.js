module.exports = {
    "env": {
        "browser": true,
        "es6": true,
        "node": true
    },
    "extends": [
        "eslint:recommended",
        'plugin:vue/recommended'
    ],
    "globals": {
        "Atomics": "readonly",
        "SharedArrayBuffer": "readonly",
        "route": "readonly"
    },
    "parserOptions": {
        "parser": "babel-eslint",
        "ecmaVersion": 2018,
        "sourceType": "module",
        "allowImportExportEverywhere": true,
    },
    "plugins": [
        "vue"
    ],
    "rules": {
        "semi": ["error", "always"],
        "indent": ["error", 4, {
            "ignoredNodes": ["TemplateLiteral"]
        }],
        "vue/html-indent": ["error", 4],
        "quotes": ["error", "single"],
        "space-before-function-paren": ["error", "never"],
        "eol-last": ["error", "always"],
        "no-new": "error",
        "no-template-curly-in-string": "error"
    }
};
