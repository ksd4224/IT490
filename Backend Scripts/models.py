from django.contrib.auth.models import AbstractUser
from django.db import models
from django.contrib.auth import authenticate

# Create your models here.

class CustomUser(AbstractUser):
    email = models.EmailField(unique=True)

    def __str__(self):
        return self.email


class UserProfile(models.Model):
    user = models.OneToOneField('CustomUser', on_delete=models.CASCADE)

#    def create_user_profile(user, **kwargs):
#        userProfile.objects.create(user=user, **kwargs)

#    def authenticate_user(email, password):
#        User = authenticate(email=email, password=password)
#        return user

