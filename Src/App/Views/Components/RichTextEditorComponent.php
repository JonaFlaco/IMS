<?php use \App\Core\Application; ?>

<template id="tpl-rich-text-editor-component">
    <div ref="editor"></div>         
</template>

<script>

    Vue.component('rich-text-editor-component', {
        template: '#tpl-rich-text-editor-component',
        props: ['value'],
        data() {
            return {
                editor: null,
                editorToolbarOptions: [
                    ['bold', 'italic', 'underline', 'strike'],        // toggled buttons
                    ['link', 'image'],
                    ['blockquote', 'code-block'],

                    [{ 'header': 1 }, { 'header': 2 }],               // custom button values
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    [{ 'script': 'sub'}, { 'script': 'super' }],      // superscript/subscript
                    [{ 'indent': '-1'}, { 'indent': '+1' }],          // outdent/indent
                    [{ 'direction': 'rtl' }],                         // text direction

                    [{ 'size': ['small', false, 'large', 'huge'] }],  // custom dropdown
                    [{ 'header': [1, 2, 3, 4, 5, 6, false] }],

                    [{ 'color': [] }, { 'background': [] }],          // dropdown with defaults from theme
                    [{ 'font': [] }],
                    [{ 'align': [] }],

                    ['clean']                                         // remove formatting button
                ]
            }
        },
        mounted() {
            this.editor = new Quill(this.$refs.editor, {
                modules: {
                    syntax: true,
                    toolbar: this.editorToolbarOptions,
                },
                theme: 'snow'
            });

            this.editor.root.innerHTML = this.value;
            
            this.editor.on('text-change', () => {
                this.$emit('input', this.editor.getText() ? this.editor.root.innerHTML : '');
            });
        },
    })

</script>