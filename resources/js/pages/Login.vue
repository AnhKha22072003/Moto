<template>
  <div class="page page-center">
    <div class="container-tight py-4">
      <form class="card card-md" @submit.prevent="handleLogin">
        <div class="card-body">
          <h2 class="card-title text-center mb-4">Đăng nhập</h2>
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input v-model="email" type="email" class="form-control" placeholder="you@example.com" required />
          </div>
          <div class="mb-2">
            <label class="form-label">
              Mật khẩu
              <span class="form-label-description">
                <a href="#">Quên mật khẩu?</a>
              </span>
            </label>
            <input v-model="password" type="password" class="form-control" placeholder="••••••••" required />
          </div>
          <div v-if="error" class="alert alert-danger mt-2">{{ error }}</div>
          <div class="form-footer">
            <button type="submit" class="btn btn-primary w-100">Đăng nhập</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import axios from 'axios'

const email = ref('')
const password = ref('')
const error = ref('')

const handleLogin = async () => {
  error.value = ''
  try {
    const response = await axios.post('/api/login', {
      email: email.value,
      password: password.value,
    })

    const token = response.data.token
    localStorage.setItem('token', token)
    window.location.href = '/motorcycles-view' 
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Đăng nhập thất bại'
  }
}
</script>
