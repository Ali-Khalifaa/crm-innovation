<template>
    <div class="rich-text-editor" :class="{ 'rich-text-editor--rtl': rtl, 'rich-text-editor--invalid': invalid }">
        <div ref="editorEl"></div>
    </div>
</template>

<script>
import Quill from 'quill';
import 'quill/dist/quill.snow.css';
import { ref, watch, onMounted, onBeforeUnmount } from 'vue';

const EMPTY_HTML = '<p><br></p>';

const normalizeHtml = (html) => {
    if (!html || html === EMPTY_HTML) return '';
    return html;
};

const applyHtml = (quill, html) => {
    if (!quill) return;

    if (!html) {
        quill.setText('');
        return;
    }

    try {
        const delta = quill.clipboard.convert({ html });
        quill.setContents(delta, 'silent');
    } catch {
        quill.root.innerHTML = html;
    }
};

export default {
    name: 'RichTextEditor',
    props: {
        modelValue: { type: String, default: '' },
        rtl: { type: Boolean, default: false },
        invalid: { type: Boolean, default: false },
        minHeight: { type: String, default: '360px' },
    },
    emits: ['update:modelValue'],
    setup(props, { emit }) {
        const editorEl = ref(null);
        let quill = null;

        onMounted(() => {
            quill = new Quill(editorEl.value, {
                theme: 'snow',
                modules: {
                    toolbar: [
                        [{ header: [1, 2, 3, false] }],
                        ['bold', 'italic', 'underline'],
                        [{ color: [] }, { background: [] }],
                        [{ list: 'ordered' }, { list: 'bullet' }],
                        [{ align: [] }],
                        ['link'],
                        ['clean'],
                    ],
                },
            });

            if (props.rtl) {
                quill.root.setAttribute('dir', 'rtl');
                quill.root.style.textAlign = 'right';
            }

            applyHtml(quill, props.modelValue);

            quill.on('text-change', () => {
                emit('update:modelValue', normalizeHtml(quill.root.innerHTML));
            });
        });

        watch(
            () => props.modelValue,
            (value) => {
                if (!quill || quill.hasFocus()) return;

                const current = normalizeHtml(quill.root.innerHTML);
                const next = normalizeHtml(value || '');

                if (current !== next) {
                    applyHtml(quill, value || '');
                }
            }
        );

        onBeforeUnmount(() => {
            quill = null;
        });

        return { editorEl, minHeight: props.minHeight };
    },
};
</script>

<style scoped>
.rich-text-editor :deep(.ql-toolbar.ql-snow) {
    background: #fff;
    border: 1px solid #dee2e6;
    border-radius: 0.375rem 0.375rem 0 0;
}

.rich-text-editor :deep(.ql-container.ql-snow) {
    background: #fff;
    border: 1px solid #dee2e6;
    border-radius: 0 0 0.375rem 0.375rem;
    font-size: 14px;
}

.rich-text-editor :deep(.ql-editor) {
    color: #1e293b !important;
    background: #fff !important;
    min-height: v-bind(minHeight);
}

.rich-text-editor :deep(.ql-editor.ql-blank::before) {
    color: #94a3b8 !important;
    font-style: normal;
}

.rich-text-editor--rtl :deep(.ql-editor) {
    direction: rtl;
    text-align: right;
}

.rich-text-editor--invalid :deep(.ql-toolbar),
.rich-text-editor--invalid :deep(.ql-container) {
    border-color: #dc3545 !important;
}
</style>
