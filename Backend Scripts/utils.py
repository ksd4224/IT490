#users/utils.py
from django.db import IntegrityError
from django.contrib.auth import authenticate
from .models import UserProfile
from .models import CustomUser

def save_user_info(first_name, last_name, email, password):
    try:
        user = CustomUser.objects.create_user(
            username=email,
            email=email,
            password=password,
            first_name=first_name,
            last_name=last_name,
        )
        create_user_profile(user)
        print("User successfully created.")
    except IntegrityError:
        print("user with this email already exists. Account createion failed.")


def create_user_profile(user, **kwargs):
    UserProfile.objects.create(user=user, **kwargs)

def authenticate_user(email, password):
    user = authenticate(email=email, password=password)
    return user

