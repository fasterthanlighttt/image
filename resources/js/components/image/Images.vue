<template>
    <div class="container">
        <table class="table" ref="table">
            <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Original</th>
                <th scope="col">With text watermark</th>
                <th scope="col">With image watermark</th>
                <th scope="col">Cropped</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="image in images" :key="image.id">
                <td>{{image.id}}</td>
                <td><a v-bind:href="'/images/originals/' + image.name" target="_blank">
                    <img v-bind:src="'/images/originals_thumbnail/' + image.original_thumbnail_name" >
                </a></td>
                <td><a v-if="image.watermark_text_name" v-bind:href="'/images/watermarks_text/' + image.watermark_text_name" target="_blank">
                    <img v-bind:src="'/images/watermarks_text_thumbnail/' + image.watermark_text_thumbnail_name" >
                </a></td>
                <td><a v-if="image.watermark_image_name" v-bind:href="'/images/watermarks_image/' + image.watermark_image_name" target="_blank">
                    <img v-bind:src="'/images/watermarks_image_thumbnail/' + image.watermark_image_thumbnail_name" >
                </a></td>
                <td><a v-if="image.cropped_name" v-bind:href="'/images/cropped/' + image.cropped_name" target="_blank">
                    <img v-bind:src="'/images/cropped_thumbnail/' + image.cropped_thumbnail_name" >
                </a></td>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
    import { bus } from '../../app'

    export default {
        mounted() {
            this.getImages();
        },
        created() {
            bus.$on('refresh-table', e => {
                this.getImages();
            })
        },
        data() {
            return {
                images: []
            }
        },
        methods: {
            getImages() {
                axios.get('/image')
                    .then((res) => {
                        //console.log(res);
                        this.images = res.data;
                    })
                    .catch((err) => {
                        //console.log(err)
                    })
            },
        }
    }

</script>