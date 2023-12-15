    <template>
    <div class="modal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            Add Quick Link
                        </h5>
                        <button type="button" class="close" @click="closeModal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" id="container-form">
                        <form @submit.prevent="submitHandler">
                            <div class="mt-4">
                                <div class="md:w-1/3">
                                    <label class="block mb-2 text-sm font-medium text-gray-900" for="section">
                                        Section
                                    </label>
                                </div>
                                <div class="mt-2">
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
                            </div>

                            <div class="mt-4">
                                <div class="md:w-1/3">
                                    <label class="block mb-2 text-sm font-medium text-gray-900" for="category">
                                        Category
                                    </label>
                                </div>
                                <div class="mt-2">
                                    <select id="category" v-model="fields.link_category" @change="fetchParentOptions" class="input-field">
                                        <option value="1">Label</option>
                                        <option value="2"> Sub Menu Lvl 1 </option>
                                        <option value="3"> Sub Menu Lvl 2 </option>
                                    </select>
                                </div>
                            </div>

                            <div v-if="fields.link_category != 1" class="mt-4">
                                <div class="md:w-1/3">
                                    <label class="block mb-2 text-sm font-medium text-gray-900" for="parentMenu">
                                        Parent Menu
                                    </label>
                                </div>
                                <div class="mt-2">
                                    <select id="parentMenu" v-model="fields.parent_link_id" class="input-field">
                                        <option v-for="parentMenu in parentOptions" :value="parentMenu.id" >
                                            {{ parentMenu.title }}
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="mt-4">
                                <div class="md:w-1/3">
                                    <label class="block mb-2 text-sm font-medium text-gray-900" for="type">
                                        Type
                                    </label>
                                </div>
                                <div class="mt-2">
                                    <select id="type" v-model="fields.type" @change="handleTypeChange" class="input-field">
                                        <option value="1">Label</option>
                                        <option value="2">Clickable</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mt-4">
                                <div class="md:w-1/3">
                                    <label class="block mb-2 text-sm font-medium text-gray-900" for="title">
                                        Title
                                    </label>
                                </div>
                                <div class="mt2">
                                    <input type="text" id="title" v-model="fields.title" class="input-field"/>
                                </div>
                            </div>

                            <div v-show="showFileUpload" class="mt-4">
                                <div class="md:w-1/3">
                                    <label class="block mb-2 text-sm font-medium text-gray-900" for="files">
                                        File Upload
                                    </label>
                                </div>
                                <div class="mt-2">
                                    <input type="file" id="files" ref="docs" class="input-field" @change="onFileChange()"/>
                                </div>
                            </div>

                            <div v-show="showLinkInput" class="mt-4">
                                <div class="md:w-1/3">
                                    <label class="block mb-2 text-sm font-medium text-gray-900" for="link">
                                        Link
                                    </label>
                                </div>
                                <div class="mt-2">
                                    <input type="text" id="link" v-model="fields.link" class="input-field"/>
                                </div>
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
                quicklink_list: {},
                showParent: false,
                file: null,
                section: null,
                docs: "",
                showFileUpload: false,
                showLinkInput: false,
                searchQuery: "",
                filteredQuickLinksList: [],
                noMatchMessage: "",
                showModal: false,
                isEditMode: false
            };
        },

        created() {
            this.initialData();
        },

        methods: {
            openModal() {
                this.isEditMode = false;
                this.showModal = true;
            },
            closeModal() {
                this.$emit('close');
            },

            filterQuicklinks() {
                if (this.searchQuery) {
                    this.filteredQuickLinksList = this.quicklink_list.filter((ql) => {
                        const query = this.searchQuery.toLowerCase();
                        return (
                            (ql.title && ql.title.toLowerCase().includes(query)) ||
                            (ql.section && ql.section.toLowerCase().includes(query)) ||
                            (ql.files && ql.files.toLowerCase().includes(query)) ||
                            (ql.link && ql.link.toLowerCase().includes(query)) ||
                            (ql.type && ql.type.toLowerCase().includes(query))
                        );
                    });

                    if (this.filteredQuickLinksList.length === 0) {
                        this.noMatchMessage = "No matches found.";
                    } else {
                        this.noMatchMessage = "";
                    }
                } else {
                    this.filteredQuickLinksList = this.quicklink_list;
                    this.noMatchMessage = "";
                }
            },


            initialData() {
                axios.get("/quicklinklist").then((response) => {
                    this.quicklink_list = response.data;

                    // Reverse the generic_list to display recently uploaded items at the top
                    this.quicklink_list.reverse();

                    // Initially, display the complete page list
                    this.filteredQuickLinksList = this.quicklink_list;
                });
            },

            onFileChange() {
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
                const url = this.isEditMode ? `/edit-quicklink/${this.fields.id}` : "/submit-current";

            axios.post(url, formData)
                .then((response) => {
                    console.log("Form submitted successfully:", response.data);

                    Swal.fire({
                        icon: "success",
                        title: "Content " + (this.isEditMode ? "updated" : "uploaded") + " successfully!",
                    }).then(() => {
                        this.showModal = false;
                        window.location.reload();
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
                this.isEditMode = true;
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
        
                this.fields.section = "";
                this.fields.link_category = "1"; 
                this.fields.parent_link_id = "";
                this.fields.type = "1"; 
                this.fields.title = "";
                this.fields.link = "";
                this.showFileUpload = false;
                this.showLinkInput = false;
                this.searchQuery = "";

        
                this.fetchParentOptions();

            
                this.filterQuicklinks();

                location.reload();
                this.isEditMode = false;
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
                                        location.reload(); 
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

    <style scoped>
        .modal-title {
            color: rgb(1, 72, 1); 
            text-align: center;
            font-size: 20px; 
            display: block; 
            margin: 0 auto; 
        }

        .modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        .modal-content {
            background-color: white;
            border-radius: 8px; /* Increase the border radius for rounded corners */
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 400px; /* Adjust the maximum width to widen the modal */
        }

        .modal-dialog {
            background-color: white;
            border-radius: 8px; /* Increase the border radius for rounded corners */
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 400px; /* Increase the maximum width to widen the modal */
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            border-bottom: 1px solid #ccc;
        }

        .modal-title {
            font-size: 20px;
        }

        .modal-body {
            padding: 20px;
        }

        .input-field {
            background-color: #F3F4F6;
            border: 1px solid #D1D5DB;
            color: #111827;
            font-size: 14px;
            border-radius: 8px;
            padding: 10px;
            outline: none;
            width: 100%;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }


        .close {
            cursor: pointer;
        }

        .heading-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .search-container {
        display: flex;
        align-items: center;
    }

    .rounded-input {
        border-radius: 8px;
        /* Adjust the border-radius value as needed */
        padding: 10px;
        width: 300px;
        /* Adjust the padding value as needed */
    }

    .search-icon {
        width: 30px;
        /* Adjust the width as needed */
        height: auto;
        margin-left: 8px;
        /* Adjust the margin as needed */
    }

    .table {
        width: 100%;
        border-collapse: collapse;
        /* Combine borders for a cleaner look */
    }

    .container{
        width: 100%;
    }

    .textArea{
        border: 2px solid #e2e0e0 ;
        resize: none;
        height: 50px;
    }

    .body{
        width: 100%;
        margin-left: auto;
        margin-right: auto;

        @media screen and (max-width: 640px) {
            /* This applies to small screens (sm:px-6) */
            padding-left: 6px;
            padding-right: 6px;
        }

        @media (min-width: 1024px) {
            padding-left: 2rem;
            padding-right: 2rem;
        }

    }

    .table{
        box-shadow: inset;
        background-color: white;
        width: 100%;
        border-radius: 10%;
    }

    .table-container {
        max-height: max-content; 
        overflow-y: auto; 
        background-color: white;
        width: 100%;
        position: relative;
        height: fit-content;
        margin-top: 10px;
    }

    .row{
        border-bottom: 2px solid #e2e0e0;
    }

    .buttonForm{
        border: 1px solid #000000;
        width: 90px;
        height: 40px;
        border-radius: 10px;
        margin: 5px;
        align-items: center;
    }

    .buttonTable{
        margin: 5px;
        align-items: center;
        text-align: center;
        width: 35;
    }

    .buttonDelTable{
        margin: 5px;
        align-items: center;
        text-align: center;
        width: 25;
    }

    .thead{
        background-color: #e3e2e2;
        text-align: center;
    }

    .label{
        font-family: Calibri;
        font-weight: bold;
        font-size: 25px;
        text-align: center;
    }

    .content-heading {
        border-bottom: 2px solid #FFCD00; 
        width: max-content;
        font-family: Calibri;
        font-weight: bold;
        font-size: 50px;
        height: 70px;
    }

    .search{
        width: 900px;
    }

    .message{
        text-align: center;
        font-family: Calibri;
        font-weight: bold;
        font-size: 30px;
    }

    .icon{
        width:30px;
        height: auto;
    }

    .modal-title {
        color: rgb(1, 72, 1); 
        text-align: center;
        font-size: 20px; 
        display: block; 
        margin: 0 auto; 
    }

    .modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }

    .modal-content {
        background-color: white;
        border-radius: 8px; /* Increase the border radius for rounded corners */
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        width: 400px; /* Adjust the maximum width to widen the modal */
    }

    .modal-dialog {
        background-color: white;
        border-radius: 8px; /* Increase the border radius for rounded corners */
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        width: 400px; /* Increase the maximum width to widen the modal */
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px;
        border-bottom: 1px solid #ccc;
    }

    .modal-title {
    font-size: 20px;
    }

    .modal-body {
        padding: 20px;
    }

    .input-field {
        background-color: #F3F4F6;
        border: 1px solid #D1D5DB;
        color: #111827;
        font-size: 14px;
        border-radius: 8px;
        padding: 10px;
        outline: none;
        width: 100%;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }


    .close {
        cursor: pointer;
    }
    </style>