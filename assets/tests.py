from django.test import TestCase

class HealthCheckTests(TestCase):
    def test_simple_math(self):
        """
        Test sederhana buat mastiin sistem test jalan.
        Nanti lu bisa ganti ini dengan test beneran.
        """
        result = 1 + 1
        self.assertEqual(result, 2)
        print("\n✅ System Health Check: AMAN")