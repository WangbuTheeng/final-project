<template>
  <div :class="cardClasses">
    <div v-if="$slots.header || title" class="card-header">
      <slot name="header">
        <h3 v-if="title" class="text-lg font-semibold text-secondary-900">
          {{ title }}
        </h3>
      </slot>
    </div>
    
    <div class="card-body">
      <slot></slot>
    </div>
    
    <div v-if="$slots.footer" class="card-footer">
      <slot name="footer"></slot>
    </div>
  </div>
</template>

<script>
export default {
  name: 'BaseCard',
  props: {
    title: {
      type: String,
      default: null
    },
    shadow: {
      type: String,
      default: 'soft',
      validator: (value) => ['none', 'soft', 'medium', 'strong'].includes(value)
    },
    hover: {
      type: Boolean,
      default: false
    },
    padding: {
      type: String,
      default: 'normal',
      validator: (value) => ['none', 'sm', 'normal', 'lg'].includes(value)
    }
  },
  computed: {
    cardClasses() {
      const classes = ['card'];
      
      // Shadow classes
      if (this.shadow !== 'soft') {
        classes.push(`shadow-${this.shadow}`);
      }
      
      // Hover effect
      if (this.hover) {
        classes.push('hover:shadow-medium transition-shadow duration-200');
      }
      
      return classes.join(' ');
    }
  }
};
</script>