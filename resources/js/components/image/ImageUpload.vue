<template>
    <div class="container">
        <form @submit.prevent>
            <div class="form-group">
                <div class="form-check">
                    <input class="form-check-input" v-model="options.is_text_watermark" type="checkbox" value="" id="textWatermark">
                    <label class="form-check-label" for="textWatermark">
                        Use text Watermark
                    </label>
                </div>
            </div>
            <div class="form-row" v-if="options.is_text_watermark">
                <div class="form-group col-md-6">
                    <label for="formGroupExampleInput">Enter text watermark</label>
                    <input type="text" v-model="options.text_watermark" name="text_watermark"
                           class="form-control" id="formGroupExampleInput" placeholder="text watermark">
                </div>
                <div class="form-check">
                    <input class="form-check-input" v-model="options.is_text_autocolor" type="checkbox" value="" id="autocolor">
                    <label class="form-check-label" for="autocolor">
                        Use text auto color
                    </label>
                </div>
            </div>
            <div class="form-group">
                <div class="form-check">
                    <input class="form-check-input" v-model="options.is_image_watermark" type="checkbox" value="" id="imageWatermark">
                    <label class="form-check-label" for="imageWatermark">
                        Use image Watermark
                    </label>
                </div>
            </div>
            <div  class="form-group" v-if="options.is_image_watermark">
                <div class="custom-file">
                    <input type="file" class="file-input" @change="onFileChanged"  id="customFile">
<!--                    <label class="file-label" for="customFile">Choose file</label>-->
                </div>
                <button class="btn btn-primary" @click="onUpload">upload</button>
            </div>
            <div class="form-group">
                <div class="form-check">
                    <input class="form-check-input" v-model="options.is_crop" type="checkbox" value="" id="cropImage">
                    <label class="form-check-label" for="cropImage">
                        Crop image
                    </label>
                </div>
            </div>
            <div class="form-row" v-if="options.is_crop">
                <div class="form-group col-md-6">
                    <label for="imageWidth">Width</label>
                    <input type="text" v-model="options.crop.width" class="form-control" id="imageWidth" placeholder="width">
                </div>
                <div class="form-group col-md-6">
                    <label for="imageHeight">Height</label>
                    <input type="text" v-model="options.crop.height" class="form-control" id="imageHeight" placeholder="height">
                </div>
            </div>
        </form>
        <div class="col">
            <div class="card">
                <div class="card-header">Upload files</div>
                <div class="card-body">
                    <vue-dropzone ref="myVueDropzone" id="dropzone" :options="dropzoneOptions"
                                  v-on:vdropzone-sending="sendingEvent"
                                  v-on:vdropzone-success="successEvent"
                    ></vue-dropzone>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

    import vue2Dropzone from 'vue2-dropzone'
    import 'vue2-dropzone/dist/vue2Dropzone.min.css'
    import { bus } from '../../app'
    export default {
        components: {
            vueDropzone: vue2Dropzone,
        },
        mounted() {
            console.log('Component mounted.')
        },
        data: function () {
            return {
                dropzoneOptions: {
                    url: '/image',
                    addRemoveLinks: true,
                    //maxFilesize: 5,
                    headers: {
                        "X-CSRF-TOKEN": document.head.querySelector("[name=csrf-token]").content
                    },
                },
                options : {
                    is_text_watermark: false,
                    is_text_autocolor: false,
                    is_image_watermark: false,
                    is_crop: false,
                    text_watermark: '',
                    selectedFile: null,
                    crop : {
                        width  : '',
                        height : ''
                    }
                }
            }
        },
        methods: {
            sendingEvent (file, xhr, formData) {
                formData.append('options', JSON.stringify(this.options));
            },
            successEvent (file, response) {
                this.refreshTable();
                console.log('blalba');
            },
            onFileChanged (event) {
                this.selectedFile = event.target.files[0]
            },
            onUpload() {
                const formData = new FormData();
                formData.append('file', this.selectedFile, this.selectedFile.name);
                axios.post('/watermark', formData)
                    .then((res) => {
                        console.log(res);
                    })
                    .catch((err) => {
                        console.log(err)
                    })
            },
            refreshTable () {
                bus.$emit('refresh-table');
            }
        },
    }
</script>