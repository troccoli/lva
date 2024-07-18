<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, Link} from '@inertiajs/vue3';
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import Dropdown from "@/Components/Dropdown.vue";
import NavLink from "@/Components/NavLink.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import ResponsiveNavLink from "@/Components/ResponsiveNavLink.vue";
</script>

<template>
  <Head title="Divisions"/>

  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Divisions</h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl h-full mx-auto sm:px-6 lg:px-8">
          <div class="p-6 text-gray-900 dark:text-gray-100">CRUD for the divisions</div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
