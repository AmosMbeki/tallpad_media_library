<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import Welcome from '@/Components/Welcome.vue';
import axios from 'axios';

export default {
    data(){
        return {
            dragging: false,
            media: [],
        };
    },

    components: {
        Welcome,
        AppLayout
    },

    methods: {
        onDroppedFiles($event){
            this.dragging = false;
            
            let files = [...$event.dataTransfer.items].filter(item => item.kind === 'file').map(item => item.getAsFile());

            this.uploadFiles(files);

            this.$refs.files.value = null
           
        },

        uploadFiles(files){
            files.forEach(file => {
                this.media.unshift({
                    file: file,
                    progress: 0,
                    error: null,
                    uploaded: false
                })
            });

            this.media.filter(media => !media.uploaded).forEach(media => {
                let form = new FormData;
                form.append('file', media.file);

                axios.post(this.route('media.store'), form, {
                    onUploadProgress: (event) => {
                        media.progress = Math.round(event.loaded * 100 / event.total)
                    }
                }).then(() => media.uploaded = true).catch(error => {
                    media.error = `Upload failed. Please try again later.`;

                    if(error?.response.status === 422){
                        media.error = error.response.data.errors.file[0];
                    }
                })
            });

            console.log(files);
        },

        onSelectedFiles($event){
            let files = [...$event.target.files];

            this.uploadFiles(files);

            this.$refs.files.value = null

        }
    }
}
</script>

<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Add new Media
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <!-- <Welcome /> -->
                    <div
                    @drop.prevent="onDroppedFiles"
                     @dragover.prevent="dragging = true"
                     @dragleave.prevent="dragging = false"
                     :class="[dragging ? 'border-indigo-500' : 'border-gray-400 flex flex-col items-center py-12 px-6 border-2 border-dashed rounded']"
                     class="flex flex-col items-center py-12 px-6 border-2 border-dashed border-gray-400 rounded">
                        <svg class="w-12 h-12 text-gray-500 dark:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13l-3 3m0 0l-3-3m3 3V8m0 13a9 9 0 110-18 9 9 0 010 18z"></path></svg>
                        <p class="text-xl text-gray-700">Drop file to upload</p>
                        <p class="mb-2 text-gray-700">or</p>
                        <label class="bg-white px-4 h-9 inline-flex items-center rounded border border-gray-300 shadow-sm text-sm font-medium text-gray-700 focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                            Select files
                            <input ref="files" @input="onSelectedFiles" name="files"  type="file"  multiple class="ml-2 sr-only">
                        </label>
                        <p class="text-xs text-gray-600 mt-4">Maximum upload file size: 512MB.</p>
                    </div>
                </div>
            </div>
        </div>

        <ul class="my-6 mx-6 bg-white block text-center rounded divide-y divide-gray-200 shadow">
            <li class="p-3 flex  block justify-between" v-for="(item, index) in media" :key="index">
                <div class="text-sm text-gray-700">{{ item.file.name }}</div>
                <div v-if="!item.uploaded && !item.error" class="w-40 bg-gray-200 rounded-full h-5 shadow-inner overflow-hidden relative flex item-center justify-center">
                    <div class="inline-block h-full bg-indigo-600 absolute top-0 left-0" :style="`width: ${item.progress}`"></div>
                    <div class="relative z-10 text-sm font-semibold text-center text-white drop-shadow text-shadow" >{{ item.progress}}%</div>
                </div>

                <div v-if="item.error" class="text-sm text-red-600">{{item.error}}</div>

                <inertia-link v-if="item.uploaded" href="#" class="text-sm text-indigo-600 underline">Edit</inertia-link>
                
            </li>
        </ul>
    </AppLayout>
</template>
