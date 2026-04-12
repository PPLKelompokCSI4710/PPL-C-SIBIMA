export default {
    '**/*.php': [
        'vendor/bin/pint'
    ],
    '**/*.{js,vue}': [
        'eslint --fix',
        'prettier --write'
    ],
    '**/*.{css,html,json,md,yaml,yml}': [
        'prettier --write'
    ]
};
