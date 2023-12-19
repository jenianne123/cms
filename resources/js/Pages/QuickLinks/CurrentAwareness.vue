<template lang="">
    <!-- ADD QUICK LINK MODAL -->
    <div v-if="showModal" class="modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add QuickLinks</h5>
                    <button type="button" class="close" @click="closeModal">
                      <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form @submit.prevent="submitCurrent">
                        <div class="mb-4">
                            <label class="block mb-2 text-sm font-medium text-gray-900" for="section">
                                Section
                            </label>
                            <select id="section" v-model="fields.section" @change="updateFileVisibility" class="input-field">
                                <option value="" disabled>
                                    Select a Section
                                </option>
                                <option value="1">
                                    Current Awareness Services
                                </option>
                                <option value="2">ULS Forms</option>
                                <option value="3">
                                    Virtual Libraries
                                </option>
                                <option value="4">
                                    Library Search Tools
                                </option>
                                <option value="5">
                                    Featured Links
                                </option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="block mb-2 text-sm font-medium text-gray-900" for="category">
                                Category
                            </label>
                            <select id="category" v-model="fields.link_category" @change="fetchParentOptions" class="input-field">
                                <option value="1">
                                    Label
                                </option>
                                <option value="2">
                                    Sub Menu Lvl 1
                                </option>
                                <option value="3">
                                    Sub Menu Lvl 2
                                </option>
                            </select>
                        </div>

                        <div v-if="fields.link_category != 1" class="mb-4">
                            <label
                                class="block mb-2 text-sm font-medium text-gray-900"
                                style="color: black"
                                for="parentMenu"
                                >Parent Menu</label
                            >
                            <select
                                id="parentMenu"
                                v-model="fields.parent_link_id"
                                class="input-field"
                            >
                                <option
                                    v-for="parentMenu in parentOptions"
                                    :value="parentMenu.id"
                                >
                                    {{ parentMenu.title }}
                                </option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label
                            class="block mb-2 text-sm font-medium text-gray-900"
                                style="color: black"
                                for="type"
                                >Type</label
                            >
                            <select
                                id="type"
                                v-model="fields.type"
                                @change="handleTypeChange"
                                class="input-field"
                            >
                                <option value="1">Label</option>
                                <option value="2">Clickable</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label
                                class="block mb-2 text-sm font-medium text-gray-900"
                                style="color: black"
                                for="title"
                                >Title</label
                            >
                            <input
                                type="text"
                                id="title"
                                v-model="fields.title"
                                class="input-field"
                            />
                        </div>

                        <div
                            v-show="showFileUpload"
                            class="mb-4"
                        >
                            <label
                                class="block mb-2 text-sm font-medium text-gray-900"
                                style="color: black"
                                for="files"
                                >File Upload</label
                            >
                            <input
                                type="file"
                                id="files"
                                ref="docs"
                                class="input-field"
                                @change="onFileChange()"
                            />
                        </div>

                        <div
                            v-show="showLinkInput"
                            class="mb-4"
                        >
                            <label
                                class="block mb-2 text-sm font-medium text-gray-900"
                                style="color: black"
                                for="link"
                                >Link</label
                            >
                            <input
                                type="text"
                                id="link"
                                v-model="fields.link"
                                class="input-field"
                            />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <button
                                type="Submit"
                                class="px-4 py-2 mr-4 font-bold text-white rounded shadow buttonForm bg-green hover:opacity-80 focus:shadow-outline focus:outline-none"
                            >
                                SUBMIT
                            </button>
                            <button
                                type="button"
                                class="px-4 py-2 mr-4 font-bold text-white rounded shadow buttonForm bg-red hover:opacity-80 focus:shadow-outline focus:outline-none"
                                @click="clearFilters"
                            >
                                CANCEL
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- SEARCH PART -->
    <div class="max-w-9xl mx-auto">
        <div class="overflow-hidden">
            <div class="body">
                <div class="flex items-center justify-center">
                    <p class="content-heading my-7">
                        Quick Links
                    </p>
                </div>
                <div class="flex justify-between items-center max-w-full">
                    <div class="flex justify-between items-center max-w-full" style="width: 600px;">
                        <!-- Add an input field for search -->
                        <input
                            type="search"
                            v-model="searchQuery"
                            @input="filterQuicklinks"
                            placeholder="Search..."
                            class="rounded-input"
                        />
                    </div>

                    <div>
                        <button @click="showRecentQuickLinks" class="w-12 h-12 shadow  text-white hover:opacity-80 focus:shadow-outline focus:outline-none font-bold rounded-lg">
                            <img src="/images/add.svg" alt="Delete" class="w-full">
                        </button>
                    </div>
                </div>

                <div class="table-container">
                    <table class="table">
                        <thead class="items-center thead">
                            <th class="py-2">Section</th>
                            <th class="py-2">Type</th>
                            <th class="py-2">Title</th>
                            <th class="py-2">Filename</th>
                            <th class="py-2">Link</th>
                            <th class="py-2">Action</th>
                        </thead>

                        <tbody>
                            <tr
                                v-for="(ql, index) in displayedQuicklinkList.slice(o, 5)"
                                :key="index"
                                class="row"
                            >
                                <td style="width: 30px; text-align:center; padding: 15px;">
                                    {{ ql.section }}
                                </td>
                                <td style="width: 200px; text-align:justify; padding: 15px;">
                                    {{ ql.type }}
                                </td>
                                <td style="width: 200px; text-align:justify; padding: 15px;">
                                    {{ ql.title }}
                                </td>
                                <td style="width: 200px; text-align:justify; padding: 15px;">
                                    {{ ql.filename }}
                                </td>
                                <td style="width: 200px; text-align:justify; padding: 15px;">
                                    {{ ql.link }}
                                </td>
                                <td class="py-2" style="text-align: center">
                                    <button @click="edit(ql.id)">
                                        <img
                                            src="/images/edit.svg"
                                            alt="Edit"
                                            class="icon"
                                        />
                                    </button>
                                    <button
                                        @click="deleteQL(ql.id)"
                                        class="ml-3"
                                    >
                                        <img
                                            src="/images/delete.svg"
                                            alt="Delete"
                                            class="icon"
                                        />
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";
import Swal from "sweetalert2";
import "/css/CurrentAwarenessStyles.css";

export default {
    data() {
        return {
            fields: {
                section: "",
            },
            parentOptions: {},
            quicklink_list: [],
            showParent: false,
            file: null,
            section: null,
            docs: "",
            showFileUpload: false,
            showLinkInput: false,
            searchQuery: "",
            filteredQuicklinkList: [],
            noMatchMessage: "",
            showModal: false,
        };
    },

    created() {
        this.initialData();
        this.filterQuicklinks();
    },

    computed: {
        displayedQuicklinkList() {
            // Check if quicklink_list is an array, otherwise use an empty array
            let sortedList = Array.isArray(this.quicklink_list) ? [...this.quicklink_list] : [];

            // Sort the list based on the 'index' property
            sortedList.sort((a, b) => a.index - b.index);

            // Filter based on search query
            if (this.searchQuery) {
            return sortedList.filter((ql) => {
                const query = this.searchQuery.toLowerCase();
                return (
                ql.title.toLowerCase().includes(query) ||
                ql.section.toLowerCase().includes(query) ||
                ql.link_category.toLowerCase().includes(query)
                );
            });
            } else {
                return sortedList;
            }
        },
    },

    methods: {
        showRecentQuickLinks() {
            this.showModal = true;
        },
        closeModal() {
            this.showModal = false;
            this.initialData();
        },

        filterQuicklinks() {
            if (this.searchQuery) {
                this.filteredQuicklinkList = this.quicklink_list.filter(
                    (ql) => {
                        const query = this.searchQuery.toLowerCase();
                        return (
                            ql.title.toLowerCase().includes(query) ||
                            ql.section.toLowerCase().includes(query) ||
                            ql.link_category.toLowerCase().includes(query)
                        );
                    }
                );

              
                if (this.filteredQuicklinkList.length === 0) {
                    this.noMatchMessage = "No matches found.";
                } else {
                    this.noMatchMessage = ""; 
                }
            } else {
      
                this.filteredQuicklinkList = [];
                this.noMatchMessage =
                    "";
            }
        },


        initialData() {
            axios.get("/quicklinklist").then((response) => {
                this.quicklink_list = response.data;
                this.quicklink_list.sort((a,b) => new Date (b.created_at) - new Date(a.created_at));
                this.filteredQuicklinkList = this.quicklink_list;
            });
        },

        onFileChange() {
            // alert("sfasfdaskf");
            // this.file = event.target.files[0];
            // alert ('sfjksafa');
            // this.docs.push(files[0]);
            this.docs = this.$refs.docs.files[0];
        },

        updateFileVisibility() {

            const showFileUploadSections = ["2", "1", "4"];
            this.showFileUpload =
                showFileUploadSections.includes(this.fields.section) &&
                this.fields.link_category !== "1";

            const showLinkInputSections = ["3", "5"];
            this.showLinkInput = showLinkInputSections.includes(
                this.fields.section
            );
        },

        submitCurrent() {
            this.updateFileVisibility();

            const formData = new FormData();
            formData.append("section", this.fields.section);
            formData.append("link_category", this.fields.link_category);
            formData.append("parent_link_id", this.fields.parent_link_id);
            formData.append("type", this.fields.type);
            formData.append("title", this.fields.title);

            if (this.showFileUpload) {
                formData.append("file", this.docs);
            } else {
                this.docs = null;
            }

            if (this.showLinkInput) {
                formData.append("link", this.fields.link);
            }

            // Determine if it's an edit or add
            const url = this.fields.id ? `/edit-quicklink/${this.fields.id}` : "/submit-current";

            axios
                .post(url, formData)
                .then((response) => {
                    console.log("Form submitted successfully:", response.data);

                    Swal.fire({
                        icon: "success",
                        title: "Content " + (this.fields.id ? "updated" : "uploaded") + " successfully!",
                    }).then(() => {
                        this.showModal = false;
                    });
                })
                .catch((error) => {
                    console.error("Error submitting form:", error);

                    Swal.fire({
                        icon: "error",
                        title: "Oops...",
                        text: "Something went wrong!",
                    });
                });
        },

        edit(id) {
        // You can fetch the existing data for the given ID from the server
        axios.get(`/get-quicklink/${id}`)
            .then((response) => {
                const editedQuickLink = response.data;
                // Assuming your fields correspond to the properties of your QuickLink model
                this.fields = { ...editedQuickLink };
                // Show the modal for editing
                this.showModal = true;
            })
            .catch((error) => {
                console.error('Error fetching QuickLink for editing', error);
            });
        },

        clearFilters() {
            this.fields={};
        },

        deleteQL(id) {
           
            console.log("first loop Deleting QuickLink with ID:", id);

        
            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, delete it?",
                cancelButtonText: "No, cancel",
                reverseButtons: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    console.log("Deleting QuickLink with ID:", id);

              
                    console.log("ID for deletion:", id);

                    axios
                        .post("/delete-content", { id: id })
                        .then((response) => {
                            console.log("Server response:", response.data);
                            if (response.data.success) {
                                
                                Swal.fire({
                                    icon: "success",
                                    title: "Successfully deleted!",
                                }).then(() => {
                                    this.initialData();
                                });
                            } else {
                                
                                Swal.fire({
                                    icon: "error",
                                    title: "Error deleting content",
                                    text: response.data.error,
                                });
                            }
                        })
                        .catch((error) => {
                            console.error("Error deleting content", error);

                          
                            Swal.fire({
                                icon: "error",
                                title: "Oops...",
                                text: "Something went wrong!",
                            });
                        });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    
                    Swal.fire("Cancelled", "Your content is safe :)", "info");
                }
            });
        },

        fetchParentOptions() {
            var section = this.fields.section;
            var linkCategory = this.fields.link_category;

            this.showParent = linkCategory >= 2 && linkCategory <= 5;

            if (this.showParent) {
                axios
                    .get(`/get-parent/${section}/${linkCategory}`)
                    .then((response) => {
                        this.parentOptions = response.data;
                    })
                    .catch((error) => {
                        console.error("Error fetching parent items", error);
                    });
            }
        },
    },
};
</script>