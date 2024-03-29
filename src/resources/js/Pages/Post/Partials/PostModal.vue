<script setup>
import {computed, ref, watch,} from 'vue'
import {
    TransitionRoot,
    TransitionChild,
    Dialog,
    DialogPanel,
    DialogTitle,
} from '@headlessui/vue'
import PostUserHeader from "@/Pages/Post/Partials/PostUserHeader.vue";
import {XMarkIcon, PaperClipIcon, ArrowUturnLeftIcon} from '@heroicons/vue/24/solid'
import {useForm, usePage} from "@inertiajs/vue3";
import ClassicEditor from "@ckeditor/ckeditor5-build-classic";
import {isImage, matchHref, matchLink} from "@/helpers.js";
import IndigoButton from "@/Components/SkyButton.vue";
import axiosClient from "@/axiosClient.js";
import UrlPreview from "@/Pages/Post/Partials/UrlPreview.vue";

const editor = ClassicEditor;
const editorConfig = {
    mediaEmbed: {
        removeProviders: ['dailymotion', 'spotify', 'youtube', 'vimeo',
            'instagram', 'twitter', 'googleMaps', 'flickr', 'facebook']
    },
    toolbar: ['bold', 'italic', '|', 'bulletedList', 'numberedList', '|', 'heading', '|', 'link', '|', 'blockQuote',]
};

const props = defineProps({
    post: {
        type: Object,
        required: true
    },
    group: {
        type: Object,
        default: null
    },
    modelValue: Boolean
});

const attachmentExtensions = usePage().props.attachmentExtensions;
const attachmentFiles = ref([]);
const attachmentErrors = ref([]);
const formErrors = ref({});

const form = useForm({
    body: '',
    group_id: null,
    attachments: [],
    deleted_file_ids: [],
    preview: {},
    preview_url: null,
    _method: 'POST'
});

const emit = defineEmits(['update:modelValue', 'hide'])

watch(() => props.post, () => {
    form.body = props.post.body || '';
    onInputPaste();
})

const show = computed({
    get: () => props.modelValue,
    set: (value) => emit('update:modelValue', value)
});

const computedAttachments = computed(() => {
    return [...attachmentFiles.value, ...(props.post.attachments || [])];
})

const showExtensionsText = computed(() => {
    for (let myFile of attachmentFiles.value) {
        const file = myFile.file;
        let parts = file.name.split('.');
        let ext = parts.pop().toLowerCase();
        if (!attachmentExtensions.includes(ext)) {
            return true;
        }
    }
    return false;
})

function closeModal() {
    show.value = false;
    emit('hide');
    resetModal();
}

function resetModal() {
    form.reset();
    formErrors.value = {};
    attachmentFiles.value = [];
    attachmentErrors.value = [];
    if (props.post.attachments) {
        props.post.attachments.forEach(file => file.deleted = false)
    }
}

function submit() {
    if (props.group) {
        form.group_id = props.group.id;
    }
    form.attachments = attachmentFiles.value.map(myFile => myFile.file)
    if (props.post.id) {
        form._method = 'PUT';
        form.post(route('post.update', props.post.id), {
            preserveScroll: true,
            onSuccess: () => {
                closeModal();
            },
            onError: (errors) => {
                processErrors(errors)
            }
        });
    } else {
        form.post(route('post.create'), {
            preserveScroll: true,
            onSuccess: () => {
                show.value = false
                closeModal();
            },
            onError: (errors) => {
                processErrors(errors)
            }
        });
    }
}

function processErrors(errors) {
    formErrors.value = errors;
    for (const key in errors) {
        if (key.includes(".")) {
            const [, index] = key.split('.');
            attachmentErrors.value[index] = errors[key];
        }
    }
}

async function onAttachmentChoose($event) {
    for (const file of $event.target.files) {
        const myFile = {
            file,
            url: await readFile(file)
        }
        attachmentFiles.value.push(myFile)
    }
    $event.target.value = null;
}

async function readFile(file) {
    return new Promise((res, rej) => {
        if (isImage(file)) {
            const reader = new FileReader();
            reader.onload = () => {
                res(reader.result);
            }
            reader.onerror = rej;
            reader.readAsDataURL(file);
        } else {
            res(null);
        }
    })
}

function removeFile(myFile) {
    if (myFile.file) {
        attachmentFiles.value = attachmentFiles.value.filter(f => f !== myFile)
    } else {
        form.deleted_file_ids.push(myFile.id)
        myFile.deleted = true;
    }
}

function undoDelete(myFile) {
    myFile.deleted = false;
    form.deleted_file_ids = form.deleted_file_ids.filter(id => myFile.id !== id);
}

function fetchPreview(url) {
    if (url === form.preview_url) return;

    form.preview_url = url;
    form.preview = {}
    if (url) {
        axiosClient.post(route('post.fetchUrlPreview'), {url})
            .then(({data}) => {
                form.preview = {
                    title: data['og:title'],
                    description: data['og:description'],
                    image: data['og:image'],
                }
            })
            .catch((error) => {
                console.error('Ошибка при получении предварительного просмотра:', error);
            })
    }
}

function onInputPaste() {
    let url = matchHref(form.body);
    if (!url) {
        url = matchLink(form.body);
    }
    fetchPreview(url);
}

</script>

<template>
    <teleport to="body">
        <TransitionRoot appear :show="show" as="template">
            <Dialog as="div" @close="closeModal" class="relative z-50">
                <TransitionChild
                    as="template"
                    enter="duration-300 ease-out"
                    enter-from="opacity-0"
                    enter-to="opacity-100"
                    leave="duration-200 ease-in"
                    leave-from="opacity-100"
                    leave-to="opacity-0"
                >
                    <div class="fixed inset-0 bg-black/25"/>
                </TransitionChild>

                <div class="fixed inset-0 overflow-y-auto">
                    <div
                        class="flex min-h-full items-center justify-center p-4 text-center"
                    >
                        <TransitionChild
                            as="template"
                            enter="duration-300 ease-out"
                            enter-from="opacity-0 scale-95"
                            enter-to="opacity-100 scale-100"
                            leave="duration-200 ease-in"
                            leave-from="opacity-100 scale-100"
                            leave-to="opacity-0 scale-95"
                        >
                            <DialogPanel
                                class="w-full max-w-md transform overflow-hidden rounded bg-white text-left align-middle shadow-xl transition-all"
                            >
                                <DialogTitle
                                    as="h3"
                                    class="flex items-center justify-between py-3 px-4 font-medium bg-gray-100 text-gray-900"
                                >
                                    {{ post.id ? 'Редактирование записи' : 'Создание записи' }}
                                    <button
                                        @click="closeModal"
                                        class="h-8 w-8 rounded-full hover:bg-black/5 transition flex items-center justify-center">
                                        <XMarkIcon class="w-4 h-4"/>
                                    </button>
                                </DialogTitle>
                                <div class="p-4">
                                    <PostUserHeader class="mb-4" :post="post" :show-time="false"/>
                                    <div
                                        v-if="formErrors.group_id"
                                        class="bg-red-400 text-white py-2 px-3 rounded mb-3">
                                        {{ formErrors.group_id }}
                                    </div>
                                    <ckeditor
                                        :editor="editor"
                                        v-model="form.body"
                                        @input="onInputPaste"
                                        :config="editorConfig">
                                    </ckeditor>
                                    <UrlPreview :preview="form.preview" :url="form.preview_url"/>
                                    <div
                                        v-if="showExtensionsText"
                                        class="border-l-4 border-amber-500 py-2 px-3 bg-amber-100 mt-3 text-gray-800">
                                        Расширения файлов могут быть следующими: <br>
                                        <small>{{ attachmentExtensions.join(', ') }}</small>
                                    </div>

                                    <div
                                        v-if="formErrors.attachments"
                                        class="border-l-4 border-red-500 py-2 px-3 bg-red-100 mt-3 text-gray-800">

                                        {{ formErrors.attachments }}
                                    </div>

                                    <div class="grid gap-3 my-3" :class="[
                                        computedAttachments.length === 1 ? 'grid-cols-1' : 'grid-cols-2'
                                    ]">
                                        <div v-for="(myFile, ind) of computedAttachments">
                                            <div
                                                class="group aspect-square bg-blue-100 flex flex-col items-center justify-center text-gray-500 relative border-2"
                                                :class="attachmentErrors[ind] ? 'border-red-500' : ''">

                                                <div
                                                    v-if="myFile.deleted"
                                                    class="absolute z-10 left-0 bottom-0 right-0 py-2 px-3 bg-black text-sm text-white flex justify-between items-center">
                                                    Будет удалено
                                                    <ArrowUturnLeftIcon @click="undoDelete(myFile)"
                                                                        class="w-4 h-4 cursor-pointer"/>
                                                </div>

                                                <button
                                                    @click="removeFile(myFile)"
                                                    class="absolute z-20 right-2 top-2 h-7 w-7 flex items-center justify-center text-2xl bg-black/30 text-white rounded-full hover:bg-black/40">
                                                    <XMarkIcon class="h-5 w-5"/>
                                                </button>

                                                <img v-if="isImage(myFile.file || myFile)"
                                                     :src="myFile.url"
                                                     class="object-contain aspect-square"
                                                     :class="myFile.deleted ? 'opacity-50' : ''"
                                                     alt="image"/>
                                                <div
                                                    v-else
                                                    class="flex flex-col justify-center items-center px-3"
                                                    :class="myFile.deleted ? 'opacity-50' : ''">
                                                    <PaperClipIcon class="w-10 h-10 mb-3"/>

                                                    <small class="text-center">
                                                        {{ (myFile.file || myFile).name }}
                                                    </small>
                                                </div>
                                            </div>
                                            <small class="text-red-500">{{ attachmentErrors[ind] }}</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="py-3 px-4 flex gap-2 ">
                                    <IndigoButton class="w-full">
                                        <PaperClipIcon class="w-4 h-4 mr-2"/>
                                        Вложить файлы
                                        <input
                                            @click.stop
                                            @change="onAttachmentChoose"
                                            type="file"
                                            multiple
                                            class="absolute left-0 top-0 right-0 bottom-0 opacity-0">
                                    </IndigoButton>
                                    <IndigoButton class="w-full" @click="submit">
                                        Сохранить
                                    </IndigoButton>
                                </div>
                            </DialogPanel>
                        </TransitionChild>
                    </div>
                </div>
            </Dialog>
        </TransitionRoot>
    </teleport>
</template>

<style scoped>

</style>
