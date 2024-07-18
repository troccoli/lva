<script setup lang="ts">
import Navigation from "@/Layouts/Navigation.vue";
</script>

<template>
  <div>
    <div class="min-h-screen">
      <Navigation/>

      <header class="sm:hidden shadow" v-if="$slots.header">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
          <slot name="header"/>
        </div>
      </header>

      <main>
        <slot/>
      </main>
    </div>
  </div>
</template>
