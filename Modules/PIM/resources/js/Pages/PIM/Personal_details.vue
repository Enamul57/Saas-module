<template>

    <Head title="PIM - Personal Details" />
    <AuthenticatedLayout>
        <div class="flex h-screen p-6 ">
            <!-- Sidebar -->
            <aside class="w-64  rounded-xl flex flex-col h-full">
                <EmployeeNav current="PIM.PersonalDetails" :employee="employee" />
            </aside>
            <main class="flex-1 flex flex-col h-full overflow-auto space-y-6">
                <!-- Navigation Bar -->
                <nav class="flex flex-wrap items-center gap-4 bg-white p-4 rounded-xl shadow-md border border-gray-200">
                    <button @click="navigateTo('pim.index')"
                        :class="['flex items-center gap-2 px-5 py-3 rounded-xl transition-all buttonSize', isActive('pim.index') ? 'primary' : 'secondaryColor']">
                        <i class="bx bx-user-plus text-xl"></i>
                        <span>Add Employee</span>
                    </button>

                    <button @click="navigateTo('PIM.EmployeeList')"
                        :class="['flex items-center gap-2 px-5 py-3 rounded-xl transition-all buttonSize', isActive('PIM.EmployeeList') ? 'primary' : 'secondaryColor']">
                        <i class="bx bx-list-ul text-xl"></i>
                        <span>Employee List</span>
                    </button>

                    <button @click="navigateTo('PIM.Reports')"
                        :class="['flex items-center gap-2 px-5 py-3 rounded-xl transition-all buttonSize', isActive('PIM.Reports') ? 'primary' : 'secondaryColor']">
                        <i class="bx bx-bar-chart-alt-2 text-xl"></i>
                        <span>Reports</span>
                    </button>
                </nav>

                <!-- Main Content -->
                <div class="mt-6 bg-white rounded-xl shadow-md p-6 h-full">
                    <div class="flex items-center justify-between">
                        <h2 class="text-2xl font-semibold text-gray-700">Personal Details</h2>
                    </div>

                    <!-- Form -->
                    <form @submit.prevent="submitForm" class="bg-white rounded-xl p-6 space-y-6">

                        <!-- Profile Photo -->
                        <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                            <div
                                class="w-32 h-32 bg-gray-100 rounded-full overflow-hidden flex items-center justify-center border">
                                <img v-if="form.imgPreview" :src="form.imgPreview" class="w-full h-full object-cover"
                                    alt="Employee Photo" />
                                <span v-else class="text-gray-400 text-sm">No Image</span>
                                <span v-if="form.errors.img" class="text-red-500 text-sm">
                                    {{ form.errors.img }}
                                </span>
                            </div>
                            <div>
                                <input type="file" ref="fileInput" accept="image/*" @change="previewLogo"
                                    class="hidden" />
                                <button class="mt-2 text-base primaryColor px-6 py-2 rounded-lg font-semibold"
                                    @click.prevent="$refs.fileInput.click()">Upload Photo</button>
                            </div>
                        </div>

                        <!-- Name Fields -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-gray-700 font-medium mb-1">First Name</label>
                                <Input v-model="form.first_name" type="text" placeholder="Enter first name" />
                                <span v-if="form.errors.first_name" class="text-red-500 text-sm">{{
                                    form.errors.first_name
                                }}</span>
                            </div>
                            <div>
                                <label class="block text-gray-700 font-medium mb-1">Middle Name</label>
                                <Input v-model="form.middle_name" type="text" placeholder="Enter middle name" />
                                <span v-if="form.errors.middle_name" class="text-red-500 text-sm">{{
                                    form.errors.middle_name
                                }}</span>
                            </div>
                            <div>
                                <label class="block text-gray-700 font-medium mb-1">Last Name</label>
                                <Input v-model="form.last_name" type="text" placeholder="Enter last name" />
                                <span v-if="form.errors.last_name" class="text-red-500 text-sm">{{ form.errors.last_name
                                }}</span>
                            </div>
                        </div>

                        <!-- Demographic Info -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-gray-700 font-medium mb-1">Date of Birth</label>
                                <Input v-model="form.dob" type="Date" />
                                <span v-if="form.errors.first_name" class="text-red-500 text-sm">{{
                                    form.errors.first_name
                                }}</span>
                            </div>

                            <div>
                                <div>
                                    <label class="block text-gray-700 font-medium mb-1">Gender</label>
                                    <Input v-model="form.gender" :type="'select'" :options="genders"></Input>
                                </div>
                                <div>
                                    <span v-if="form.errors.gender" class="text-red-500 text-sm">{{
                                        form.errors.gender
                                    }}</span>
                                </div>
                            </div>

                            <div>
                                <div>
                                    <label class="block text-gray-700 font-medium mb-1">Marital Status</label>
                                    <Input v-model="form.marital_status" :type="'select'"
                                        :options="marital_statuses"></Input>
                                </div>
                                <div>
                                    <span v-if="form.errors.marital_status" class="text-red-500 text-sm">{{
                                        form.errors.marital_status
                                    }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Nationality & Religion -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-gray-700 font-medium mb-1">Nationality</label>
                                <Input v-model="form.nationality" placeholder="Enter nationality" :type="'select'"
                                    :options="nationalities" />
                                <span v-if="form.errors.nationality" class="text-red-500 text-sm">{{
                                    form.errors.nationality
                                }}</span>
                            </div>
                            <div>
                                <label class="block text-gray-700 font-medium mb-1">Religion (optional)</label>
                                <Input v-model="form.religion" type="text" placeholder="Enter religion" />
                                <span v-if="form.errors.religion" class="text-red-500 text-sm">{{
                                    form.errors.religion
                                }}</span>
                            </div>
                            <div>
                                <label class="block text-gray-700 font-medium mb-1">Blood Group (optional)</label>
                                <Input v-model="form.blood_group" placeholder="Enter nationality" :type="'select'"
                                    :options="blood_groups" />
                                <span v-if="form.errors.blood_group" class="text-red-500 text-sm">{{
                                    form.errors.blood_group
                                }}</span>
                            </div>
                        </div>

                        <!-- ID Details -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-gray-700 font-medium mb-1">National ID </label>
                                <Input v-model="form.national_id" type="text" placeholder="Enter National ID" />
                                <span v-if="form.errors.national_id" class="text-red-500 text-sm">{{
                                    form.errors.national_id
                                }}</span>
                            </div>
                            <div>
                                <label class="block text-gray-700 font-medium mb-1">Passport Number (if
                                    applicable)</label>
                                <Input v-model="form.passport_number" type="text" placeholder="Enter passport number" />
                                <span v-if="form.errors.passport_number" class="text-red-500 text-sm">{{
                                    form.errors.passport_number
                                }}</span>
                            </div>
                        </div>

                        <!-- Digital Signature -->
                        <div>
                            <label class="block text-gray-700 font-medium mb-1">Digital Signature (optional)</label>
                            <input type="file" ref="signatureInput" accept="image/*" @change="previewSignature"
                                class="hidden" />
                            <div class="flex items-center gap-4">
                                <div class="w-36 h-18 border rounded-md flex items-center justify-center bg-gray-50">
                                    <img v-if="form.signaturePreview" :src="form.signaturePreview"
                                        class="h-full object-contain" alt="Signature" />
                                    <span v-else class="text-gray-400 text-sm px-3 py-1">No Signature</span>
                                </div>
                                <button class="text-base primaryColor px-6 py-2 rounded-lg font-semibold"
                                    @click.prevent="$refs.signatureInput.click()">Upload Signature</button>
                            </div>
                            <span v-if="form.errors.signature" class="text-red-500 text-sm">{{
                                form.errors.signature
                            }}</span>
                        </div>
                        <!-- Add Attachments -->
                        <!-- Add Attachments -->
                        <!-- Attachments -->
                        <div>
                            <label class="block text-gray-700 font-medium mb-1">Attachments (optional)</label>
                            <input type="file" ref="attachmentInput" multiple @change="previewAttachments"
                                class="hidden" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" />

                            <div class="flex flex-col gap-3">
                                <!-- Upload Button -->
                                <button class="text-base primaryColor px-6 py-2 rounded-lg font-semibold w-fit"
                                    @click.prevent="$refs.attachmentInput.click()">
                                    Upload Attachments
                                </button>

                                <!-- ====== EXISTING ATTACHMENTS FROM DATABASE ====== -->
                                <div v-if="props.employee?.personal_details?.attachments?.length"
                                    class="flex flex-wrap gap-3 border-t border-gray-200 pt-3">
                                    <div v-for="(file, index) in props.employee.personal_details.attachments"
                                        :key="'existing-' + index"
                                        class="flex items-center gap-3 bg-gray-50 rounded-lg px-3 py-2 border w-fit">
                                        <!-- Thumbnail if image -->
                                        <img v-if="isImage(file.file_name)" :src="file.file_path"
                                            class="w-12 h-12 object-cover rounded-md border"
                                            alt="Existing attachment" />
                                        <!-- File icon otherwise -->
                                        <div v-else
                                            class="w-12 h-12 flex items-center justify-center bg-white rounded-md border">
                                            <i class="bx bx-file text-gray-500 text-2xl"></i>
                                        </div>

                                        <div class="flex flex-col">
                                            <span class="text-sm text-gray-700 truncate max-w-[140px]">
                                                {{ file.file_name }}
                                            </span>
                                            <span class="text-xs text-gray-500">{{ file.file_type }}</span>
                                        </div>

                                        <!-- Download Button -->
                                        <a :href="file.file_path" download
                                            class="text-blue-600 hover:text-blue-800 text-sm ml-2">
                                            <i class="bx bx-download text-lg"></i>
                                        </a>
                                    </div>
                                </div>

                                <!-- ====== NEWLY SELECTED ATTACHMENTS (PREVIEW BEFORE UPLOAD) ====== -->
                                <div v-if="form.attachmentsPreview.length"
                                    class="flex flex-wrap gap-3 border-t border-gray-200 pt-3">
                                    <div v-for="(file, index) in form.attachmentsPreview" :key="'preview-' + index"
                                        class="flex items-center gap-3 bg-gray-100 rounded-lg px-3 py-2 border w-fit">
                                        <!-- Thumbnail for image -->
                                        <img v-if="file.preview" :src="file.preview"
                                            class="w-12 h-12 object-cover rounded-md border" alt="Attachment preview" />

                                        <!-- File icon -->
                                        <div v-else
                                            class="w-12 h-12 flex items-center justify-center bg-white rounded-md border">
                                            <i class="bx bx-file text-gray-500 text-2xl"></i>
                                        </div>

                                        <div class="flex flex-col">
                                            <span class="text-sm text-gray-700 truncate max-w-[140px]">
                                                {{ file.name }}
                                            </span>
                                            <span class="text-xs text-gray-500">{{ file.type || "Unknown" }}</span>
                                        </div>

                                        <!-- Remove button -->
                                        <button type="button" class="text-red-500 hover:text-red-600 ml-1"
                                            @click="removeAttachment(index)">
                                            <i class="bx bx-x text-xl"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- ====== Empty State ====== -->
                                <span
                                    v-if="!form.attachmentsPreview.length && !props.employee?.personal_details?.attachments?.length"
                                    class="text-gray-400 text-sm">
                                    No attachments uploaded
                                </span>
                            </div>
                        </div>



                        <!-- Submit Button -->
                        <div class="flex justify-end mt-6">
                            <button type="submit" class="primary px-6 py-2 rounded-lg" :disabled="form.processing">
                                {{ form.processing ? 'Saving...' : 'Save Personal Details' }}
                            </button>
                        </div>
                    </form>
                </div>
            </main>
        </div>
    </AuthenticatedLayout>
</template>

<script setup lang="ts">
import { Head, router, useForm, usePage } from "@inertiajs/vue3";
import { ref, computed, reactive, onMounted } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Input from "@/Components/Input.vue";
import { Notyf } from "notyf";
import "notyf/notyf.min.css";
import EmployeeNav from '../../Components/EmployeeNav.vue';

const props = defineProps({
    employee: {
        required: true,
        type: Object,
    }
});
const marital_statuses = [
    'Married',
    'Single',
    'Other',
];
const genders = [
    'Male',
    'Female',
]
const blood_groups = [
    'A+',
    'A-',
    'B+',
    'B-',
    'O+',
    'O-',
    'AB+',
    'AB-',
]
const notyf = new Notyf({
    duration: 3000,
    position: { x: "right", y: "top" },
    dismissible: true,
});

const page = usePage();
const currentRoute = computed(() => page.url);
const isActive = (routeName: string) => route().current() === routeName;

function navigateTo(routeName: string) {
    router.visit(route(routeName));
}
const nationalities = ref([]);
onMounted(async () => {
    const res = await fetch("/json/nationality.json");
    nationalities.value = await res.json();
})
console.log(props.employee);
const form = useForm({
    img: null,
    imgPreview: props.employee.img ?? null,
    signature: null,
    signaturePreview: props.employee?.personal_details?.signature ?? null,

    first_name: props.employee.first_name ?? "",
    middle_name: props.employee.middle_name ?? "",
    last_name: props.employee.last_name ?? "",
    dob: props.employee?.personal_details?.formatted_dob ?? null,
    gender: props.employee?.personal_details?.gender ?? null,
    marital_status: props.employee?.personal_details?.marital_status ?? null,
    nationality: props.employee?.personal_details?.nationality ?? null,
    religion: props.employee?.personal_details?.religion ?? null,
    blood_group: props.employee?.personal_details?.blood_group ?? null,
    national_id: props.employee?.personal_details?.national_id ?? null,
    passport_number: props.employee?.personal_details?.passport_number ?? null,
    attachments: [],
    attachmentsPreview: [],
});

function previewLogo(e: Event) {
    const file = (e.target as HTMLInputElement).files?.[0];
    if (file) {
        form.img = file;
        form.imgPreview = URL.createObjectURL(file);
    }
}

function previewSignature(e: Event) {
    const file = (e.target as HTMLInputElement).files?.[0];
    if (file) {
        form.signature = file;
        form.signaturePreview = URL.createObjectURL(file);
    }
}

const isImage = (fileName: string) => /\.(jpg|jpeg|png|gif|webp)$/i.test(fileName);
// 🆕 Preview attachments
function previewAttachments(e: Event) {
    const files = (e.target as HTMLInputElement).files;
    if (files) {
        form.attachments = Array.from(files);

        form.attachmentsPreview = Array.from(files).map((file) => (
            {
                name: file.name,
                type: file.type,
                preview: file.type.startsWith('/image') ? URL.createObjectURL(file) : null,
            }
        ))
    }
}

// 🆕 Remove individual attachment
function removeAttachment(index: number) {
    form.attachments.splice(index, 1);
    form.attachmentsPreview.splice(index, 1);
}

const submitForm = () => {
    form.post(route("PIM.storePersonalDetails", { employee: props.employee.id }), {
        preserveScroll: true,
        onSuccess: () => {
            notyf.success("Personal details saved successfully!");
            form.reset(); // reset form if needed
        },
        onError: () => {
            // Errors automatically populate `form.errors`
            console.log(form.errors);
            notyf.error("Please correct the highlighted errors!");
        },
    });
};
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
