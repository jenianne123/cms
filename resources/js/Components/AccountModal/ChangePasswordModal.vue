<template>
    <div class="modal" v-if="show">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Manage Account</h5>
                    <button type="button" class="close" @click="closeChangePassModal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form @submit.prevent="saveAccount">
                      <div class="mb-4">
                          <label for="password" class="block mb-2 text-sm font-medium text-gray-900">
                              Password
                          </label>
                          <input type="password" name="password" id="password" v-model="fields.password" class="input-field" autocomplete="new-password" required>
                          <p v-if="fields.password && !isPasswordValid" class="text-red-600">
                              Password must be at least 8 characters long and contain at least one uppercase letter, one lowercase letter, and one number.
                          </p>
                      </div>

                        <div class="mb-4">
                            <label for="password_confirmation" class="block mb-2 text-sm font-medium text-gray-900">Confirm Password</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" v-model="fields.password_confirmation" class="input-field" autocomplete="new-password" @input="checkPasswordMatch" required>
                            <p v-if="fields.password_confirmation && !passwordMatch" class="text-red-600">Password and Confirm Password do not match.</p>
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
      fields: {
        password: '',
      },
    },

    data() {
      return {
        passwordMatch: true,
        isPasswordValid: true,
      };
    },
  
    methods: {
      initialData() {
        axios.get(`/userlist`).then((response) => {
          this.user_list = response.data;
          // Initially, display the complete User list
          this.filteredUserList = this.user_list;
        });
      },
  
      closeChangePassModal() {
        this.$emit('close'); // Emit the 'close' event to close the modal
      },
  
      validatePasswordFormat() {
        // Password format validation logic
        const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/;
        this.isPasswordValid = passwordRegex.test(this.fields.password);
      },
  
      checkPasswordMatch() {
        this.passwordMatch = this.fields.password === this.fields.password_confirmation;
      },
  
      clearFormFields() {
        this.fields = {}; 
      },

      edit(id) {
        axios.get(`/user-data/${id}`).then(response => {
          this.fields = response.data;
        });
      },

      saveAccount() {
        // Validate password format
        this.validatePasswordFormat();

        if (!this.isPasswordValid) {
          return; // Do not proceed with saving if password format is not valid
        }

        axios.post('/submit-password', this.fields).then((response) => {
          if (response.data) {
            // User data has been updated successfully
            Swal.fire({
                icon: "success",
                title: "Password Changed Successfully!",
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