<template>
  
    <!-- Uploaded -->
    <div class="max-w-9xl mx-auto" >
        <div class="overflow-hidden ">
            <div class="body">
                <div class="flex items-center justify-center">
                    <p class="content-heading my-7">
                        Frequently Asked Questions
                    </p>
                </div>
                <div class="flex justify-between items-center max-w-full" >
                    <div class="flex justify-between items-center max-w-full" style="width: 600px;">
                        <input
                            type="search"
                            class="relative textArea block w-[1px] min-w-0 flex-auto rounded border bg-transparent bg-clip-padding text-base font-normal leading-[1.6] text-neutral-700 outline-none transition duration-200 ease-in-out focus:z-[3] focus:border-primary focus:text-neutral-700 focus:shadow-[inset_0_0_0_1px_rgb(59,113,202)] focus:outline-none dark:border-neutral-600 dark:text-neutral-200 dark:placeholder:text-neutral-200 dark:focus:border-primary"
                            placeholder="Search"
                            aria-label="Search"
                            aria-describedby="button-addon1"
                            v-model="searchQuery"
                            @input="filterFaqs"
                        />
                    </div>
                    <div class="ml-40">
                        <button @click="openModal" class="w-12 h-12 shadow  text-white hover:opacity-80 focus:shadow-outline focus:outline-none font-bold rounded-lg">
                            <img src="/images/add.svg" alt="Delete" class="w-full">
                        </button>
                        <FAQ-modal :show="showModal" @close="closeModal" :fields="fields" @save="submitFaq"/>
                    </div>
                </div>
                <div v-if="filteredFaqList.length > 0" class="table-container" >
                    <table class="table">
                        <thead class="thead items-center">
                            <th>#</th>
                            <th>Questions</th>
                            <th>Answer</th>
                            <th>Action</th>
                        </thead>
                        <tbody>
                            <tr v-for="(faq, index) in filteredFaqList.slice(0, 5)" :key="index" class="row">
                                <td style="width: 30px; text-align:center; padding: 15px;">{{ index + 1 }}</td>
                                <td style="width: 500px; text-align:justify; padding: 15px;">{{ faq.question }}</td>
                                <td style="width: 700px; text-align:justify; padding: 15px;">{{ faq.answer }}</td>
                                <td style="width: 200px; text-align: center;">
                                    <button @click="edit(faq.id)" >
                                        <img src="/images/edit.svg" alt="" class="icon">
                                    </button>
                                    <button @click="deleteFaqs(faq.id)" class="ml-3">
                                        <img src="/images/delete.svg" alt="Delete" class="icon">
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <edit-faq-modal :show="showEditFaqModal" @close="closeEditFaqModal" :fields="fields" @save="saveEditedData" />
                <p v-if="noMatchMessage" class="message">{{ noMatchMessage }}</p>
            </div>
        </div>
    </div>
</template>

<script>
    import FAQModal from '@/Components/FaqModal/FAQModal.vue';
    import EditFaqModal from '@/Components/FaqModal/EditFaqModal.vue';
    import axios from 'axios';

    export default {
        components:{
            FAQModal,
            EditFaqModal,
        },

        data() {
            return {
                fields: {
                    question: '',
                    answer: '',
                },
                faq_list: [],
                searchQuery: "", // To store the search query
                filteredFaqList: [], // To store the filtered FAQ list
                noMatchMessage: "",
                showModal: false,
                showEditFaqModal: false,

            };
        },

        created() {
            this.initialData();
        },

        methods: {
            initialData() {
                axios.get(`/faqlist`).then((response) => {
                    this.faq_list = response.data;
                    
                    this.faq_list.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));

                    // Initially, display the complete FAQ list
                    this.filteredFaqList = this.faq_list;
                });
            },

            deleteFaqs(id){
                if(confirm('Are you sure?')){
                    axios.post('/delete-faqs',{id}).then(({data})=>{
                        if(data){
                            alert('success');
                            this.$emit('success');
                            this.initialData();
                        }else{
                            alert('error');
                        }
                    })
                }else{
                    alert('Cancelled')
                }
            },

            edit(id) {
                axios.get(`/faqs-data/${id}`).then(response => {
                    this.openEditFaqModal();
                    this.fields = response.data;
                });
            },

            openEditFaqModal() {
                this.showEditFaqModal = true;
            },

            closeEditFaqModal() {
                this.initialData();
                this.showEditFaqModal = false
            },

            // Function to filter the FAQ list based on the search query
            filterFaqs() {
                if (this.searchQuery) {
                    // Filter the FAQ list when a search query is entered
                    this.filteredFaqList = this.faq_list.filter((faq) => {
                        const query = this.searchQuery.toLowerCase();
                        return (
                            (faq.question && faq.question.toLowerCase().includes(query)) ||
                            (faq.answer && faq.answer.toLowerCase().includes(query))
                        );
                    });

                    // Check if there are no matches and set the "no match" message
                    if (this.filteredFaqList.length === 0) {
                        this.noMatchMessage = "No matches found.";
                    } else {
                        this.noMatchMessage = ""; // Clear the "no match" message
                    }
                } else {
                    // Display the complete FAQ list when the query is empty
                    this.filteredFaqList = this.faq_list;
                    this.noMatchMessage = ""; // Clear the "no match" message
                }
            },

            clearFormFields() {
                this.fields = {};
            },

            openModal() {
                this.showModal = true;
                this.fields = {
                    question: '',
                    answer: '',
                }
            },

            closeModal() {
                this.initialData();
                this.showModal = false;
            },
        },
    };
</script>

<style>
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
        margin-bottom: 40px;
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
</style>