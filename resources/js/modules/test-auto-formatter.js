// Simple Node.js test for AutoFormatter
// Run with: node test-auto-formatter.js

class AutoFormatter {
  constructor() {
    this.formatters = {
      phone: this.formatPhone.bind(this),
      name: this.formatName.bind(this),
      email: this.formatEmail.bind(this)
    };
  }
  
  formatPhone(value) {
    const cleaned = value.replace(/\D/g, '');
    if (cleaned.length <= 4) return cleaned;
    if (cleaned.length <= 8) return `${cleaned.slice(0, 4)}-${cleaned.slice(4)}`;
    return `${cleaned.slice(0, 4)}-${cleaned.slice(4, 8)}-${cleaned.slice(8, 11)}`;
  }
  
  formatName(value) {
    return value
      .toLowerCase()
      .split(' ')
      .map(word => word.charAt(0).toUpperCase() + word.slice(1))
      .join(' ');
  }
  
  formatEmail(value) {
    return value.toLowerCase();
  }
  
  getSuggestedDomains(partialEmail) {
    const domains = ['@gmail.com', '@yahoo.com', '@outlook.com'];
    const parts = partialEmail.split('@');
    
    if (parts.length === 2 && parts[1].length > 0) {
      return domains.filter(domain => 
        domain.toLowerCase().startsWith('@' + parts[1].toLowerCase())
      );
    }
    
    if (parts.length === 2 && parts[1].length === 0) {
      return domains;
    }
    
    return [];
  }
  
  format(value, formatterType) {
    if (!this.formatters[formatterType]) {
      console.error('Invalid formatter type');
      return value;
    }
    return this.formatters[formatterType](value);
  }
}

// Test suite
console.log('=== AutoFormatter Test Suite ===\n');

const formatter = new AutoFormatter();
let passCount = 0;
let failCount = 0;

function test(name, actual, expected) {
  const passed = actual === expected;
  if (passed) {
    console.log(`✓ ${name}`);
    console.log(`  Input: "${actual}" matches expected: "${expected}"\n`);
    passCount++;
  } else {
    console.log(`✗ ${name}`);
    console.log(`  Expected: "${expected}"`);
    console.log(`  Got: "${actual}"\n`);
    failCount++;
  }
}

// Phone formatting tests
console.log('--- Phone Number Formatting Tests ---');
test('Phone: 081234567890 → 0812-3456-7890', 
  formatter.formatPhone('081234567890'), 
  '0812-3456-7890');
test('Phone: 0812 → 0812', 
  formatter.formatPhone('0812'), 
  '0812');
test('Phone: 08123456 → 0812-3456', 
  formatter.formatPhone('08123456'), 
  '0812-3456');
test('Phone: 0812-3456-789 (with dashes) → 0812-3456-789', 
  formatter.formatPhone('0812-3456-789'), 
  '0812-3456-789');

// Name formatting tests
console.log('--- Name Formatting Tests ---');
test('Name: john doe → John Doe', 
  formatter.formatName('john doe'), 
  'John Doe');
test('Name: MARY JANE → Mary Jane', 
  formatter.formatName('MARY JANE'), 
  'Mary Jane');
test('Name: alice BOB charlie → Alice Bob Charlie', 
  formatter.formatName('alice BOB charlie'), 
  'Alice Bob Charlie');

// Email formatting tests
console.log('--- Email Formatting Tests ---');
test('Email: User@Example.COM → user@example.com', 
  formatter.formatEmail('User@Example.COM'), 
  'user@example.com');
test('Email: TEST@GMAIL.COM → test@gmail.com', 
  formatter.formatEmail('TEST@GMAIL.COM'), 
  'test@gmail.com');

// Email domain suggestion tests
console.log('--- Email Domain Suggestion Tests ---');
test('Suggestions: user@gm → [@gmail.com]', 
  JSON.stringify(formatter.getSuggestedDomains('user@gm')), 
  JSON.stringify(['@gmail.com']));
test('Suggestions: user@y → [@yahoo.com]', 
  JSON.stringify(formatter.getSuggestedDomains('user@y')), 
  JSON.stringify(['@yahoo.com']));
test('Suggestions: user@o → [@outlook.com]', 
  JSON.stringify(formatter.getSuggestedDomains('user@o')), 
  JSON.stringify(['@outlook.com']));
test('Suggestions: user@ → all domains', 
  JSON.stringify(formatter.getSuggestedDomains('user@')), 
  JSON.stringify(['@gmail.com', '@yahoo.com', '@outlook.com']));
test('Suggestions: user (no @) → []', 
  JSON.stringify(formatter.getSuggestedDomains('user')), 
  JSON.stringify([]));

// Summary
console.log('\n=== Test Summary ===');
console.log(`Total: ${passCount + failCount}`);
console.log(`✓ Passed: ${passCount}`);
console.log(`✗ Failed: ${failCount}`);

if (failCount === 0) {
  console.log('\n🎉 All tests passed!');
  process.exit(0);
} else {
  console.log('\n❌ Some tests failed!');
  process.exit(1);
}
