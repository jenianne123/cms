<template>
    <div class="modal" v-if="show">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Edit Account</h5>
            <button type="button" class="close" @click="closeEditAccModal">
              <span>&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="saveAccount">
              <div class="mb-4">
                <label for="name" class="block mb-2 text-sm font-medium text-gray-900">Name</label>
                <input type="text" name="name" id="name" v-model="fields.name" class="input-field" required>
              </div>
              <div class="mb-4">
                <label for="email" class="block mb-2 text-sm font-medium text-gray-900">Email</label>
                <input type="email" name="email" id="email" v-model="fields.email" class="input-field" required >
              </div>
              <div class="mb-4">
                <label for="role" class="block mb-2 text-sm font-medium text-gray-900">Role</label>
                <select id="role" name="role" v-model="fields.role" class="input-field" required>
                  <option value="" disabled>Select a Role</option>
                  <option value="1">Admin</option>
                  <option value="2">Librarian</option>
                </select>
              </div>
              <div class="mb-4"
                v-if="
                  fields.role == '2'
                " 
              >
                <div class="checkbox-container">
                  <p>Generic Pages: </p>
                  <input
                    type="checkbox"
                    id="generic_pages"
                    style="float: right;"
                    v-model="fields.generic_pages"
                    @change="handleChange"
                  />
                </div>
                <div class="checkbox-container">
                  <p>FAQs: </p>
                  <input
                    type="checkbox"
                    id="faq"
                    style="float: right;"
                    v-model="fields.faq"
                    @change="handleChange"
                  />
                </div>
                <div class="checkbox-container">
                  <p>Articles: </p>
                  <input
                    type="checkbox"
                    id="articles"
                    style="float: right;"
                    v-model="fields.articles"
                    @change="handleChange"
                  />
                </div>
                <div class="checkbox-container">
                  <p>Images: </p>
                  <div style="float: right;">
                    <input
                      type="checkbox"
                      id="images"
                      style="float: right;"
                      v-model="fields.images"
                      @change="handleChange"
                    />
                  </div>
                </div>
                <div class="checkbox-container">
                  <p>Quick Links: </p>
                  <input
                    type="checkbox"
                    id="accounts"
                    style="float: right;"
                    v-model="fields.accounts"
                    @change="handleChange"
                  />
                </div>
              </div>
              <div class="text-center mb-4">
                <button type="submit" class="bg-green text-white rounded px-4 py-2">
                  Save
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
</template>

<script>
import axios from 'axios';
import Swal from 'sweetalert2';

export default {
  props: {
    show: Boolean,
    fields: Object,
  },

  data() {
    return {
      passwordMatch: true, // Initially assume passwords match
      changePasswordModal: false,

    };
  },

  computed: {
    showChangePasswordModal() {
      return this.changePasswordModal;
    },
  },

  methods: {
    initialData() {
        axios.get(`/userlist`).then((response) => {
            this.user_list = response.data;

            // Fetch user access data
            this.user_list.forEach(async (user) => {
                await this.getUserAccess(user);
            });

            this.user_list.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
            
            // Initially, display the complete User list
            this.filteredUserList = this.user_list;
        });
    },

    closeEditAccModal() {
      this.$emit('close'); // Emit the 'close' event to close the modal
    },

    edit(id){
      axios.get(`/user-data/${id}`).then(response=> {
          this.fields = response.data;
      });
    },

    checkPasswordMatch() {
      this.passwordMatch = this.fields.password === this.fields.password_confirmation;
    },

    clearFormFields() {
      this.fields = {};
    },

    saveAccount() {
      // Validate if at least one checkbox is checked for Librarian role
      if (this.fields.role === 2 && !this.areAnyCheckboxesChecked()) {
        alert('Please check at least one checkbox.');
        return; // Do not proceed with saving if no checkbox is checked
      }

      const userAccess = {
        generic_pages: this.fields.generic_pages ? 1 : 0,
        faq: this.fields.faq ? 1 : 0,
        images: this.fields.images ? 1 : 0,
        articles: this.fields.articles ? 1 : 0,
        accounts: this.fields.accounts ? 1 : 0,
      };

      const acc_id = 0;

      axios.post('/submit-edit-form', this.fields).then((response) => {
        axios.post(`/submit-user`, { ...this.fields, userAccess }).then((response) => {
          if (response.data) {
            this.fetchUserList();
            Swal.fire({
                icon: "success",
                title: "Content is Updated Successfully!",
            }).then(() => {
              this.$emit('close');
            });
          } else {
            Swal.fire({
                icon: "error",
                title: "Oops...",
                text: "Something went wrong!",
            });
          }
        });
      });
    },

    areAnyCheckboxesChecked() {
      // Helper function to check if at least one checkbox is checked
      return (
        this.fields.generic_pages ||
        this.fields.faq ||
        this.fields.articles ||
        this.fields.images ||
        this.fields.accounts
      );
    },

    fetchUserList() {
      axios.get('/userlist').then((response) => {
        this.user_list = response.data;
      });
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
      z-index: 999;
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
      text-align: left;
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